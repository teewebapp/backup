<?php

namespace Tee\Backup\Tests;

use Tee\System\Tests\TestCase;
use Config;
use Tee\Backup\Storage\Local\Storage;

class LocalStorageTest extends AbstractStorageTest
{

    public function getStorage()
    {
        $tempName = tempnam(sys_get_temp_dir(), 'localbackup');
        unlink($tempName);
        mkdir($tempName);
        $localStorage = new Storage(1, array(
            'type' => 'local',
            'directory' => $tempName
        ));
        return $localStorage;
    }

}