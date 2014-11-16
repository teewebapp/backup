<?php

namespace Tee\Backup\Tests;

use Tee\System\Tests\TestCase;
use Config;

class DirectoryBackupTest extends TestCase {

    public function testCreateDirectoryBackup()
    {
        $service = $this->app->make('Tee\Backup\Services\BackupService');
        $backuper = $this->app->make('Tee\Backup\Directory\Backup');
        $tempDir = $service->getTemporaryDirectory();
        $directory = __DIR__;
        $fileBackup = $backuper->backup($tempDir, $directory);
        $this->assertEquals($directory, $fileBackup->directory);
        $this->assertNotEmpty($fileBackup->filename);
        $this->assertNotEmpty($fileBackup->md5);
        $this->assertTrue(file_exists($fileBackup->filename));
        $factory->removeDirectory($fileBackup->filename);
        $this->assertFalse(file_exists($fileBackup->filename));
    }

}