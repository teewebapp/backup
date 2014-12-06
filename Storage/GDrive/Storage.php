<?php

namespace Tee\Backup\Storage\GDrive;

use Google_Service_Drive;
use Google_Auth_AssertionCredentials;
use Google_Client;

class Storage implements \Tee\Backup\Storage\Storage
{
    private $credentials;
    private $client;
    private $drive;

    public $chunkSizeBytes;

    /**
     * Receives parameters from configuration
     */
    public function __construct($configuration)
    {
        $this->chunkSizeBytes = 1 * 1024 * 1024;

        $client_email = $configuration['clientEmail'];
        $private_key = file_get_contents($configuration['privateKeyFile']);
        $scopes = array(Google_Service_Drive::DRIVE);
        $this->credentials = new Google_Auth_AssertionCredentials(
            $client_email,
            $scopes,
            $private_key,
            $configuration['privateKeyPassword']
        );
    }

    /**
     * Login (with parameters from configuration)
     */
    public function login()
    {
        $this->client = new Google_Client();
        $this->client->setAssertionCredentials($this->credentials);
        if ($this->client->getAuth()->isAccessTokenExpired()) {
            $this->client->getAuth()->refreshTokenWithAssertion();
        }
        $this->drive = new Google_Service_Drive($this->client);
        return true;
    }

    /**
     * Change the current directory of remote storage
     */
    public function changeDirectory($path)
    {

    }

    /**
     * Upload a file to current directory
     */
    public function uploadFile($localPath)
    {
        if(!file_exists($localPath))
            throw new \Exception('File not exists');

        $file = new \Google_Service_Drive_DriveFile();
        $file->title = basename($localPath);
        $chunkSizeBytes = $this->chunkSizeBytes;
        $this->client->setDefer(true);
        $request = $this->drive->files->insert($file);

        $media = new \Google_Http_MediaFileUpload(
            $this->client,
            $request,
            'text/plain',
            null,
            true,
            $chunkSizeBytes
        );
        $media->setFileSize(filesize($localPath));

        // Upload the various chunks. $status will be false until the process is
        // complete.
        $status = false;
        $handle = fopen($localPath, "rb");
        while (!$status && !feof($handle)) {
            $chunk = fread($handle, $chunkSizeBytes);
            $status = $media->nextChunk($chunk);
        }

        // The final value of $status will be the data from the API for the object
        // that has been uploaded.
        $result = false;
        if($status != false) {
            $result = $status;
        }

        fclose($handle);
        // Reset to the client to execute requests immediately in the future.
        $this->client->setDefer(false);

        if($result)
            return new File($result, $this);
    }

    /**
     * List files from current directory
     */ 
    public function listFiles()
    {
        $rawFiles = $this->drive->files->listFiles(array())->getItems();
        $results = array();
        foreach($rawFiles as $file) {
            $results[] = new File($file, $this);
        }
        return $results;
    }

    public function getByName($name)
    {
        foreach($this->listFiles() as $file) {
            if($file->getName() == $name)
                return $file;
        }
    }

    public function getClient()
    {
        return $this->client;
    }

}