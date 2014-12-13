<?php

namespace Tee\Backup\Services;
use Config;

class StorageService
{
    public function uploadFile($filename) {
        foreach($this->listStorage() as $storage) {
            $storage->login();
            $storage->uploadFile($filename);
            $storage->logout();
        }
    }

    public function listStorage()
    {
        $results = [];
        foreach(Config::get('backup::backup.storages') as $storageConfig) {
            $results[] = $this->createStorage($storageConfig);
        }
        return $results;
    }

    public function createStorage($storageConfig)
    {
        if($storageConfig['type'] == 'gdrive') {
            $gdrive = new \Tee\Backup\Storage\GDrive\Storage($storageConfig);
            return $gdrive;
        } else {
            throw new \Exception("Invalid storage type: #{$storageConfig['type']}");
        }
    }
}