<?php
namespace Tee\Backup\Directory;
use App;


/**
 * Create a backup of an Directory
 * @author Anderson Danilo
 */
class Backup {
    public function backup($tempDir, $targetDir) {
        $fileBackup = new \Tee\Backup\Package\Directory();
        if(!$targetDir)
            throw new \Exception("targetDir cannot be empty");
        $fileBackup->directory = $targetDir;
        $name = md5($targetDir).'.tar';
        $fileBackup->filename = "$tempDir/$name";
        $zippy = App::make('backup.zippy');
        $zippy->create($fileBackup->filename, $fileBackup->directory);
        $fileBackup->md5 = md5_file($fileBackup->filename);
        return $fileBackup;
    }
}