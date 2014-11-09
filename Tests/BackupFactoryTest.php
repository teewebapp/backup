<?php

namespace Tee\Backup\Tests;

use Tee\System\Tests\TestCase;

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

}