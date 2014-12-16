<?php

namespace Tee\Backup\Storage\Local;

class Storage implements \Tee\Backup\Storage\Storage
{
    private $directory;
    private $id;

    /**
     * Receives parameters from configuration
     */
    public function __construct($id, $configuration)
    {
        $this->id = $id;
        $this->directory = $configuration['directory'];
    }

    /**
     * Login (with parameters from configuration)
     */
    public function login()
    {
        return true;
    }

    /**
     * Do nothing 
     */
    public function logout() {
        // do nothing
    }

    /**
     * Upload a file to current directory
     */
    public function uploadFile($localPath)
    {
        $baseName = basename($localPath);
        copy($localPath, implode(DIRECTORY_SEPARATOR, [$this->directory, $baseName]));
        return new File($baseName, $this);
    }

    /**
     * List files from current directory
     */ 
    public function listFiles()
    {
        $directory = $this->directory;
        $rawFiles = array_filter(scandir($this->directory), function($item) use($directory) {
            if(in_array($item, ['.', '..']))
                return false;
            if(is_dir(implode(DIRECTORY_SEPARATOR, [$directory, $item])))
                return false;
            return true;
        });

        $results = array();
        foreach($rawFiles as $file) {
            $results[] = new File($file, $this);
        }
        return $results;
    }

    public function idToAbsolutePath($id) {
        return implode(DIRECTORY_SEPARATOR, [$this->directory, $id]);
    }

    public function exists($id) {
        return file_exists($this->idToAbsolutePath($id));
    }

    public function getById($id) {
        if($this->exists($id))
            return new File($id, $this);
    }

    public function delete($id) {
        unlink($this->idToAbsolutePath($id));
    }

    public function getByName($name)
    {
        foreach($this->listFiles() as $file) {
            if($file->getName() == $name)
                return $file;
        }
    }

    public function getId()
    {
        return $this->id;
    }

}