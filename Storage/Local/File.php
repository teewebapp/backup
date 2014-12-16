<?php

namespace Tee\Backup\Storage\Local;

class File implements \Tee\Backup\Storage\File
{
    public $rawFile;

    public function __construct($rawFile, $storage)
    {
        $this->rawFile = $rawFile;
        $this->storage = $storage;
    }

    public function getName() {
        return $this->rawFile;
    }

    private function getAbsolutePath() {
        return $this->storage->idToAbsolutePath($this->getId());
    }

    /**
     * Get file size in bytes
     */
    public function getSize() {
        return filesize($this->getAbsolutePath());
    }

    /**
     * Return a id for manipulation
     */
    public function getId() {
        return $this->rawFile;
    }

    public function download($localPath)
    {
        copy($this->getAbsolutePath(), $localPath);
    }

    /**
     * self delete this file 
     */
    public function delete() {
        return $this->storage->delete($this->getId());
    }

    /**
     * get its storage
     */
    public function getStorage()
    {
        return $this->storage;
    }
}