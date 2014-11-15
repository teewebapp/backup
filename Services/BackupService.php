<?php

namespace Tee\Backup\Services;
use App, Config;

class BackupService {
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
        $factory = App::make('Tee\Backup\BackupFactory');
        $package = $factory->createBackup($databases, $directories);
        return $package;
    }
}