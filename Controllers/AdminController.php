<?php

namespace Tee\Backup\Controllers;

use View, URL, App, Redirect, Input, Queue, Artisan;
use Tee\System\Breadcrumbs;

class AdminController extends \Tee\Admin\Controllers\AdminBaseController
{
    public function index() {
        View::share('pageTitle', 'Backup');
        Breadcrumbs::addCrumb(
            'Backup',
            URL::route("admin.backup.index")
        );

        $storageService = App::make('Tee\Backup\Services\StorageService');

        try {
            $listFiles = $storageService->listFiles();
        } catch(\Exception $e) {
            View::share('errorMessage', 'Houve um erro ao listar os arquivos: '.$e->getMessage());
            $listFiles = array();
        }

        return View::make('backup::admin.index', array(
            'listBackup' => $listFiles,
        ));
    }

    public function create()
    {
        $executed = false;
        Queue::push(function($job) use($executed) {
            Artisan::call('backup:create');
            $executed = true;
            $job->delete();
        });
        return Redirect::to(route('admin.backup.index', [
            'successMessage' => $executed ?
                'Backup criado com sucesso' :
                'O Backup está sendo criado e estará disponível em breve'
        ]));
    }

    public function download($id)
    {
        $storageId = Input::get('storageId');

        $storageService = App::make('Tee\Backup\Services\StorageService');
        $storage = $storageService->getStorageById($storageId);
        $storage->login();

        $file = $storage->getById($id);
        $tempName = tempnam(sys_get_temp_dir(), 'tmp');
        $file->download($tempName);
        $storage->logout();

        $this->sendFile($tempName, $file->getName());
        unlink($tempName);
        exit;
    }

    /**
     * Start a big file download on Laravel Framework 4.0 / 4.1
     * Source (originally for Laravel 3.*) : http://stackoverflow.com/questions/15942497/why-dont-large-files-download-easily-in-laravel
     * @param  string $path    Path to the big file
     * @param  string $name    Name of the file (used in Content-disposition header)
     * @param  array  $headers Some extra headers
     */
    public function sendFile($path, $name = null, array $headers = array()){
        if (is_null($name)) $name = basename($path);

        $file = new \Symfony\Component\HttpFoundation\File\File($path);
        $mime = $file->getMimeType();

        // Prepare the headers
        $headers = array_merge(array(
            'Content-Description'       => 'File Transfer',
            'Content-Type'              => $mime,
            'Content-Transfer-Encoding' => 'binary',
            'Expires'                   => 0,
            'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma'                    => 'public',
            'Content-Length'            => \File::size($path),
            'Content-Disposition'       => 'attachment; filename='.$name
        ), $headers);

        $response = new \Symfony\Component\HttpFoundation\Response('', 200, $headers);

        // If there's a session we should save it now
        if (\Config::get('session.driver') !== ''){
            \Session::save();
        }

        session_write_close();
        if (ob_get_length()) ob_end_clean();
        
        $response->sendHeaders();
        
        // Read the file
        if ($file = fopen($path, 'rb')) {
            while(!feof($file) and (connection_status()==0)) {
                print(fread($file, 1024*8));
                flush();
            }
            fclose($file);
        }

        // Finish off, like Laravel would
        \Event::fire('laravel.done', array($response));
        $response->send();
    }

    public function destroy($id) {
        $storageId = Input::get('storageId');

        $storageService = App::make('Tee\Backup\Services\StorageService');
        $storage = $storageService->getStorageById($storageId);
        $storage->login();
        $storage->delete($id);
        $storage->logout();

        return Redirect::to(route('admin.backup.index', [
            'successMessage' => 'Arquivo apagado com sucesso'
        ]));
    }
}
