<?php

namespace Tee\Backup\Storage;

interface Storage
{
    /**
     * Receives parameters from configuration
     */
    public function __construct($id, $configuration);

    /**
     * Login (with parameters from configuration)
     */
    public function login();

    /**
     * Logout
     */
    public function logout();

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

    /**
     * Get a file by its id
     */
    public function getById($id);

    /**
     * Return a id for manipulation
     */
    public function getId();


    /**
     * Remove a file by its id
     */
    public function delete($id);
}