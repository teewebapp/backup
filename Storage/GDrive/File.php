<?php

namespace Tee\Backup\Storage\GDrive;

class File implements \Tee\Backup\Storage\File
{
    public $rawFile;

    public function __construct($rawFile, $storage)
    {
        $this->rawFile = $rawFile;
        $this->storage = $storage;
    }

    public function getName() {
        return $this->rawFile->originalFilename;
    }

    /**
     * Get file size in bytes
     */
    public function getSize() {
        return $this->rawFile->fileSize;
    }

    /**
     * Return a id for manipulation
     */
    public function getId() {
        return $this->rawFile->id;
    }

    public function download($localPath)
    {
        // Partial download
        // Partial download involves downloading only a specified portion of a file. You can specify the portion of the file you want to dowload by using a byte range with the Range header. For example:
        // Range: bytes=500-999
        $downloadUrl = $this->rawFile->getDownloadUrl();
        if ($downloadUrl) {
            $request = new \Google_Http_Request($downloadUrl, 'GET', null, null);
            $this->storage->getClient()->getAuth()->sign($request);
            
            set_time_limit(0);
            $fp = fopen ($localPath, 'w+');//This is the file where we save the    information
            $ch = curl_init(str_replace(" ","%20",$request->getUrl()));//Here is the file we are downloading, replace spaces with %20
            curl_setopt($ch, CURLOPT_TIMEOUT, 500);
            curl_setopt($ch, CURLOPT_FILE, $fp); // write curl response to file
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $headers = array();
            foreach($request->getRequestHeaders() as $key => $value)
                $headers[] = $key.': '.$value;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_exec($ch); // get curl response
            curl_close($ch);
            fclose($fp);
        } else {
            // The file doesn't have any content stored on Drive.
            return null;
        }
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