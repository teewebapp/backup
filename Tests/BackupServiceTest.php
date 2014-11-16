<?php

namespace Tee\Backup\Tests;

use Tee\System\Tests\TestCase;
use Config;

class BackupServiceTest extends TestCase
{

    public function testCreateDefaultBackup()
    {
        $service = $this->app->make('Tee\Backup\Services\BackupService');
        $package = $service->createDefaultBackup();
        $this->assertTrue(file_exists($package->filename));
        $this->assertTrue(!empty($package));

        $service->remove($package->filename);
        $this->assertFalse(file_exists($package->filename));
    }

    public function testGetRelativePath()
    {
        $service = $this->app->make('Tee\Backup\Services\BackupService');

        $ds = DIRECTORY_SEPARATOR;

        $result = $service->getRelativePath(__DIR__, __DIR__);
        $this->assertEquals('', $result);

        $result = $service->getRelativePath(__DIR__, realpath(__DIR__.$ds.'..'));
        $this->assertEquals('..', $result);

        $result = $service->getRelativePath(__DIR__, realpath(__DIR__.$ds.'..'.$ds.'..'));
        $this->assertEquals('../..', $result);

        $result = $service->getRelativePath(__DIR__.$ds.'..', __DIR__);
        $this->assertEquals(basename(__DIR__), $result);

        $result = $service->getRelativePath('/home/test/a/b.php', '/home/test/a/c/z.php');
        $this->assertEquals('c/z.php', $result);

        $result = $service->getRelativePath('/home/test/i/b.php', '/home/test/a/c/z.php');
        $this->assertEquals('../a/c/z.php', $result);
    }
}