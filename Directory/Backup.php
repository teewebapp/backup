<?php
namespace Tee\Backup\Directory;
use App;


/**
 * Create a backup of an Directory
 * @author Anderson Danilo
 */
class Backup {
    public function backup($tempDir, $targetDir) {
        $service = App::make('Tee\Backup\Services\BackupService');
        $fileBackup = new \Tee\Backup\Package\Directory();
        if(!$targetDir)
            throw new \Exception("targetDir cannot be empty");
        $fileBackup->directory = $service->getBaseRelativePath($targetDir);
        $name = md5($targetDir).'.zip';
        $filename = "$tempDir/$name";
        $fileBackup->filename = $name;
        $zippy = App::make('backup.zippy');

        $tempTargetDir = tempnam(sys_get_temp_dir(), 'tmp');
        unlink($tempTargetDir);
        mkdir($tempTargetDir);

        $success = \File::copyDirectory($targetDir, $tempTargetDir);
        $zippy->create($filename, $tempTargetDir);
        $fileBackup->md5 = md5_file($filename);

        \File::deleteDirectory($tempTargetDir);

        return $fileBackup;
    }
}