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

    public function createBackup(array $databases, array $directories)
    {
        $package = new Package\Package();
        $tempDir = $this->getTemporaryDirectory();
        foreach($directories as $directory)
            $package->push($this->createFileBackup($tempDir, $directory));
        foreach($databases as $database)
            $package->push($this->createDatabaseBackup($tempDir, $database));
        $this->compressBackup($package, $tempDir);
        $this->removeDirectory($tempDir);
        return $package;
    }

    public function compressBackup(Package\Package $package, $tempDir) {
        $filename = tempnam(sys_get_temp_dir(), 'backup').'.tar.gz';
        $package->filename = $filename;
        $meta = $package->toJson();
        file_put_contents($tempDir.'/meta.json', $meta);
        $zippy = App::make('backup.zippy');
        $zippy->create($filename, $tempDir);
        return $filename;
    }

    public function getTemporaryDirectory()
    {
        if(!sys_get_temp_dir())
            throw new Exception("Not found temp dir");
        $pathName = sys_get_temp_dir().'/'.uniqid();
        if(!$pathName)
            throw new \Exception('Error on create name');
        mkdir($pathName, 0700);
        if(!file_exists($pathName))
            throw new \Exception('Error on mkdir');
        return $pathName;
    }

    public function removeDirectory($directory)
    {
        if(!$directory)
            throw new Exception("directory is required");
        $tempDir = sys_get_temp_dir();
        if(!sys_get_temp_dir())
            throw new Exception("Not found temp dir");
        if(substr($directory, 0, strlen($tempDir)) != $tempDir)
            throw new Exception("Directory outside temp dir");
        $process = new Process("rm -rf $directory");
        $process->run();        
    }
}