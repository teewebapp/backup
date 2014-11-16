<?php

namespace Tee\Backup\Tests;

use Tee\System\Tests\TestCase;
use Config;

class DirectoryBackupTest extends TestCase
{

    public function testCreateDirectoryBackup()
    {
        $service = $this->app->make('Tee\Backup\Services\BackupService');
        $backuper = $this->app->make('Tee\Backup\Directory\Backup');
        $tempDir = $service->getPackageTemporaryDirectory();
        $directory = __DIR__;
        $fileBackup = $backuper->backup($tempDir, $directory);
        $this->assertEquals($service->getBaseRelativePath($directory), $fileBackup->directory);
        $this->assertNotEmpty($fileBackup->filename);
        $this->assertNotEmpty($fileBackup->md5);
        $this->assertTrue(file_exists($tempDir.DIRECTORY_SEPARATOR.$fileBackup->filename));
        $service->remove($tempDir.DIRECTORY_SEPARATOR.$fileBackup->filename);
        $this->assertFalse(file_exists($tempDir.DIRECTORY_SEPARATOR.$fileBackup->filename));
        $service->remove($tempDir);
    }

}