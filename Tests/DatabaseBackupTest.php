<?php

namespace Tee\Backup\Tests;

use Tee\System\Tests\TestCase;
use Config;

class DatabaseBackupTest extends TestCase {

    public function testCreateDatabaseBackup()
    {
        $service = $this->app->make('Tee\Backup\Services\BackupService');
        $backuper = $this->app->make('Tee\Backup\Database\Backup');
        $tempDir = $service->getTemporaryDirectory();
        $directory = __DIR__;
        $databaseBackup = $backuper->backup($tempDir, Config::get('database.default'));
        $this->assertEquals(Config::get('database.default'), $databaseBackup->connection);
        $this->assertNotEmpty($databaseBackup->filename);
        $this->assertNotEmpty($databaseBackup->md5);
        $this->assertTrue(file_exists($databaseBackup->filename));
        $service->removeDirectory($databaseBackup->filename);
        $this->assertFalse(file_exists($databaseBackup->filename));
    }

}