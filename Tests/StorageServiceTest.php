<?php

namespace Tee\Backup\Tests;

use Tee\System\Tests\TestCase;
use Config;

class StorageServiceTest extends TestCase
{
    public function testListStorage()
    {
        $service = $this->app->make('Tee\Backup\Services\StorageService');
        $storages = $service->listStorage();
        $this->assertTrue(count($storages) > 0);
        foreach($storages as $storage) {
            $this->assertTrue($storage instanceof \Tee\Backup\Storage\Storage);
        }
    }

}