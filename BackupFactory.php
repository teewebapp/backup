<?php

namespace Tee\Backup;

use Symfony\Component\Process\Process;

class BackupFactory {

    public function getTemporaryDirectory() {
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

    public function removeDirectory($directory) {
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

    public function createBackup() {
        $tempDir = $this->getTemporaryDirectory();
        $fileBackup = $this->createFileBackup($tempDir, $targetDir);
    }

    public function createFileBackup($tempDir, $targetDir) {
        $fileBackup = new Package\Files();
        $fileBackup->directory = $targetDir;
        $name = md5($targetDir).'.tar';
        $fileBackup->filename = "$tempDir/$name";
        $process = new Process("tar -cvf $fileBackup->filename $fileBackup->directory");
        $process->run();
        $fileBackup->md5 = md5_file($fileBackup->filename);
        return $fileBackup;
    }

    public function createDatabaseBackup() {

    }

    public function compressBackup() {
        
    }
}