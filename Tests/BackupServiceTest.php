<?php

namespace Tee\Backup\Tests;

use Tee\System\Tests\TestCase;
use Config;

class BackupServiceTest extends TestCase {

    public function testCreateDefaultBackup()
    {
        $service = $this->app->make('Tee\Backup\Services\BackupService');
        $package = $service->createDefaultBackup();
        $this->assertTrue(file_exists($package->filename));
        $this->assertTrue(!empty($package));

        $service->remove($package->filename);
        $this->assertFalse(file_exists($package->filename));
    }
}