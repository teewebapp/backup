<?php

namespace Tee\Backup;

use Symfony\Component\Process\Process;
use Config;

class BackupFactory
{

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

    public function createFileBackup($tempDir, $targetDir) {
        $fileBackup = new Package\Files();
        if(!$targetDir)
            throw new \Exception("targetDir cannot be empty");
        $fileBackup->directory = $targetDir;
        $name = md5($targetDir).'.tar';
        $fileBackup->filename = "$tempDir/$name";
        $process = new Process("tar -cvf $fileBackup->filename $fileBackup->directory");
        $process->run();
        $fileBackup->md5 = md5_file($fileBackup->filename);
        return $fileBackup;
    }

    public function createDatabaseBackup($tempDir, $connection)
    {
        $databaseBackup = new Package\Database();
        $databaseBackup->connection = $connection;
        $info = Config::get("database.connections.$connection");
        if(!$info)
            throw new Exception("Connection $connection not found");

        if($info['driver'] == 'mysql') {
            $filename = "$tempDir/".md5($connection).'.sql';
            $this->dumpMysqlDatabase($info, $filename);
        } else if($info['driver'] == 'sqlite') {
            $filename = "$tempDir/".md5($connection).'.sqlite';
            $this->dumpSqliteDatabase($info, $filename);
        } else {
            throw new \Exception("Backup not avaliable to driver: {$info['driver']}");
        }
        $databaseBackup->filename = $filename;
        $databaseBackup->md5 = md5_file($filename);
        return $databaseBackup;
    }

    public function dumpMysqlDatabase($config, $filename) {
        $username = $config['username'];
        $password = $config['password'];
        $database = $config['database'];
        $process = new Process("mysqldump -u$username -p$password $database > $filename");
        $process->run();
        return true;
    }

    public function dumpSqliteDatabase($config, $filename) {
        $database = $config['database'];
        copy($database, $filename);
        return true;
    }

    public function compressBackup(Package\Package $package, $tempDir) {
        $filename = tempnam(sys_get_temp_dir(), 'backup').'.tar.gz';
        $package->filename = $filename;
        $meta = $package->toJson();
        file_put_contents($tempDir.'/meta.json', $meta);
        $process = new Process("tar -caf $filename $tempDir");
        $process->run();
        return $filename;
    }
}