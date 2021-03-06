<?php

namespace Tee\Backup\Storage\GDrive;

use Google_Service_Drive;
use Google_Auth_AssertionCredentials;
use Google_Client;
use Google_Service_Drive_DriveFile;

class Storage implements \Tee\Backup\Storage\Storage
{
    private $credentials;
    private $client;
    private $drive;
    private $id;
    private $folder;
    private $gdriveFolder;

    public $chunkSizeBytes;

    /**
     * Receives parameters from configuration
     */
    public function __construct($id, $configuration)
    {
        $this->chunkSizeBytes = 1 * 1024 * 1024;
        $this->id = $id;
        $this->folder = $configuration['folder'];

        $clientEmail = $configuration['clientEmail'];
        if($configuration['privateKeyContent'])
        {
            $privateKey = $configuration['privateKeyContent'];
        }
        else
        {
            $privateKey = file_get_contents($configuration['privateKeyFile']);
        }

        $scopes = array(Google_Service_Drive::DRIVE);
        $this->credentials = new Google_Auth_AssertionCredentials(
            $clientEmail,
            $scopes,
            $privateKey,
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

        if($this->folder)
            $this->changeFolder($this->folder);

        return true;
    }

    public function changeFolder($folder) {

        $files = $this->drive->files->listFiles();
        foreach ($files->getItems() as $item) {
            if ($item->getTitle() == $folder) {
                $this->gdriveFolder = $item;
                break;
            }
        }
        if(!$this->gdriveFolder) {
            $file = new Google_Service_Drive_DriveFile();
            $file->setTitle($folder);
            $file->setMimeType('application/vnd.google-apps.folder');

            $this->gdriveFolder = $this->drive->files->insert($file, array(
                'mimeType' => 'application/vnd.google-apps.folder',
            ));
        }
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
        if(!file_exists($localPath))
            throw new \Exception('File not exists');

        $file = new \Google_Service_Drive_DriveFile();
        $file->title = basename($localPath);
        if($this->gdriveFolder) {
            $parent = new \Google_Service_Drive_ParentReference();
            $parent->setId($this->gdriveFolder->getId());
            $file->setParents(array($parent));
        }
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
        $params = array();

        if($this->gdriveFolder)
            $params['q'] = "'{$this->gdriveFolder->getId()}' in parents";

        $rawFiles = $this->drive->files->listFiles($params)->getItems();
        $results = array();
        foreach($rawFiles as $file) {
            $results[] = new File($file, $this);
        }
        return $results;
    }

    public function getById($id) {
        $rawFile = $this->drive->files->get($id);
        if($rawFile)
            return new File($rawFile, $this);
    }

    public function delete($id) {
        return $this->drive->files->delete($id);
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

    public function getId()
    {
        return $this->id;
    }
}