<?php

namespace Tee\Backup\Tests;

use Tee\System\Tests\TestCase;
use Config;

class BackupFactoryTest extends TestCase {

    public function testCreateFileBackup()
    {
        $factory = $this->app->make('Tee\Backup\BackupFactory');
        $tempDir = $factory->getTemporaryDirectory();
        $directory = __DIR__;
        $fileBackup = $factory->createFileBackup($tempDir, $directory);
        $this->assertEquals($directory, $fileBackup->directory);
        $this->assertNotEmpty($fileBackup->filename);
        $this->assertNotEmpty($fileBackup->md5);
        $this->assertTrue(file_exists($fileBackup->filename));
        $factory->removeDirectory($fileBackup->filename);
        $this->assertFalse(file_exists($fileBackup->filename));
    }

    public function testCreateDatabaseBackup()
    {
        $factory = $this->app->make('Tee\Backup\BackupFactory');
        $tempDir = $factory->getTemporaryDirectory();
        $directory = __DIR__;
        $databaseBackup = $factory->createDatabaseBackup($tempDir, Config::get('database.default'));
        $this->assertEquals(Config::get('database.default'), $databaseBackup->connection);
        $this->assertNotEmpty($databaseBackup->filename);
        $this->assertNotEmpty($databaseBackup->md5);
        $this->assertTrue(file_exists($databaseBackup->filename));
        $factory->removeDirectory($databaseBackup->filename);
        $this->assertFalse(file_exists($databaseBackup->filename));
    }

}