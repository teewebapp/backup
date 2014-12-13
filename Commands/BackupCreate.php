<?php

namespace Tee\Backup\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App;

class BackupCreate extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'backup:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an default backup';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $backupService = App::make('Tee\Backup\Services\BackupService');
        $storageService = App::make('Tee\Backup\Services\StorageService');
        
        $package = $backupService->createDefaultBackup();

        $this->info("Backup saved on $package->filename");

        if($this->option('upload')) {
            $storageService->uploadFile($package->filename);
            $this->info("Backup uploaded");
        }

        if($this->option('delete')) {
            unlink($package->filename);
            $this->info("Backup deleted from $package->filename");
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            //array('example', InputArgument::REQUIRED, 'An example argument.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('upload', null, InputOption::VALUE_OPTIONAL, 'Faz o upload do arquivo.', 1),
            array('delete', null, InputOption::VALUE_OPTIONAL, 'Deleta o arquivo após fazer o upload.', 1),
        );
    }

}
