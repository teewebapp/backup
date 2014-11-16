<?php

namespace Tee\Backup\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Tee\Page\Models\Page;

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
        $service = App::make('Tee\Backup\Services\BackupService');
        $package = $service->createDefaultBackup();
        $this->info("Backup saved on $package->filename");
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
            //array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        );
    }

}
