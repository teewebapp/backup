<?php

namespace Tee\Backup\Services;

use Tee\Backup\Package\Package;
use App, Config;

use RecursiveIteratorIterator, RecursiveDirectoryIterator, FilesystemIterator;

class BackupService
{
    public function createDefaultBackup()
    {
        $databases = Config::get('backup::backup.databases');
        $directories = Config::get('backup::backup.directories');

        foreach($databases as $k => $database) {
            if($database == 'default')
                $databases[$k] = Config::get('database.default');
        }
        if(!$databases)
            $databases = array();
        if(!$directories)
            $directories = array();
        $package = $this->createBackup($databases, $directories);
        return $package;
    }

    public function createBackup(array $databases, array $directories)
    {
        $package = new Package();
        $tempDir = $this->getPackageTemporaryDirectory();
        $databaseBackuper = App::make('Tee\Backup\Database\Backup');
        $directoryBackuper = App::make('Tee\Backup\Directory\Backup');
        foreach($directories as $directory)
            $package->push($directoryBackuper->backup($tempDir, $directory));
        foreach($databases as $database)
            $package->push($databaseBackuper->backup($tempDir, $database));
        $this->compressBackup($package, $tempDir);
        $this->remove($tempDir);
        return $package;
    }

    public function compressBackup(Package $package, $tempDir)
    {
        $name = tempnam(sys_get_temp_dir(), 'backup_'.date('Y-m-d_H:i:s').'_');
        unlink($name);
        $filename = $name.'.zip';
        $package->filename = $filename;
        $meta = $package->toJson();
        file_put_contents($tempDir.'/meta.json', $meta);
        $zippy = App::make('backup.zippy');
        $zippy->create($filename, $tempDir);
        return $filename;
    }

    public function getPackageTemporaryDirectory()
    {
        $ds = DIRECTORY_SEPARATOR;
        if(!$this->getTemporaryDirectory())
            throw new Exception("Not found temp dir");
        $pathName = $this->getTemporaryDirectory().$ds.uniqid();
        if(!$pathName)
            throw new \Exception('Error on create name');
        mkdir($pathName, 0700);
        if(!file_exists($pathName))
            throw new \Exception('Error on mkdir');
        return $pathName;
    }

    public function getTemporaryDirectory()
    {
        return sys_get_temp_dir();
    }

    public function remove($removePath)
    {
        if(!$removePath)
            throw new Exception("directory is required");
        $tempDir = sys_get_temp_dir();
        if(!sys_get_temp_dir())
            throw new Exception("Not found temp dir");
        if(substr($removePath, 0, strlen($tempDir)) != $tempDir)
            throw new Exception("Directory outside temp dir");
        if(is_file($removePath))
        {
            unlink($removePath);
        }
        else
        {
            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($removePath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
                $path->isDir() ? rmdir($path->getPathname()) : unlink($path->getPathname());
            }
            rmdir($removePath);
        }
    }

    public function getBaseRelativePath($to)
    {
        $basePath = base_path();
        return $this->getRelativePath($basePath, $to);
    }

    public function getRelativePath($from, $to)
    {
        if(file_exists($from))
            $from = realpath($from);
        if(file_exists($to))
            $to = realpath($to);
        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
        $from = str_replace('\\', '/', $from);
        $to   = str_replace('\\', '/', $to);

        $from     = explode('/', $from);
        $to       = explode('/', $to);
        $relPath  = $to;

        foreach($from as $depth => $dir) {
            // find first non-matching dir
            if($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    $relPath[0] = $relPath[0];
                }
            }
        }
        return rtrim(implode('/', $relPath), '/');
    }
}