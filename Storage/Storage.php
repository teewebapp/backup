<?php

namespace Tee\Backup\Storage;

interface Storage
{
    /**
     * Receives parameters from configuration
     */
    public function __construct($configuration);

    /**
     * Login (with parameters from configuration)
     */
    public function login();

    /**
     * Change the current directory of remote storage
     */
    public function changeDirectory($path);

    /**
     * Upload a file to current directory
     */
    public function uploadFile($localPath);

    /**
     * List files from current directory
     */ 
    public function listFiles();

    /**
     * Get a file by its name
     */
    public function getByName($fileName);
}