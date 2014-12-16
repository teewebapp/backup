<?php

namespace Tee\Backup\Tests;

use Tee\System\Tests\TestCase;
use Config;
use Tee\Backup\Storage\GDrive\Storage;
use Tee\Backup\Storage\File as IFile;

class GoogleDriveTest extends AbstractStorageTest
{
    public function getStorage()
    {
        $configStorages = Config::get('backup::backup.storages');
        $configGdrive = $configStorages[0];
        $gdrive = new Storage(1, $configGdrive);
        return $gdrive;
    }
}