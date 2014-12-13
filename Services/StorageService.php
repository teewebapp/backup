<?php

namespace Tee\Backup\Services;
use Config;

class StorageService
{
    private $storages = null;

    public function uploadFile($filename) {
        foreach($this->listStorage() as $storage) {
            $storage->login();
            $storage->uploadFile($filename);
            $storage->logout();
        }
    }

    public function listStorage()
    {
        if(is_null($this->storages))
        {
            $results = [];
            foreach(Config::get('backup::backup.storages') as $pos => $storageConfig) {
                $results[] = $this->createStorage($pos + 1, $storageConfig);
            }
            $this->storages = $results;
        }
        return $this->storages;
    }

    public function getStorageById($id)
    {
        foreach($this->listStorage() as $storage) {
            if($storage->getId() == $id) {
                return $storage;
            }
        }
    }

    public function listFiles() {
        $results = array();
        foreach($this->listStorage() as $storage) {
            $storage->login();
            foreach($storage->listFiles() as $file) {
                $results[] = $file;
            }
            $storage->logout();
        }
        return $results;
    }

    public function createStorage($id, $storageConfig)
    {
        if($storageConfig['type'] == 'gdrive') {
            $gdrive = new \Tee\Backup\Storage\GDrive\Storage($id, $storageConfig);
            return $gdrive;
        } else {
            throw new \Exception("Invalid storage type: #{$storageConfig['type']}");
        }
    }
}