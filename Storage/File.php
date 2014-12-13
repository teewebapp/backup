<?php

namespace Tee\Backup\Storage;

interface File
{
    public function getName();

    /**
     * Get file size in bytes
     */
    public function getSize();

    /**
     * Delete a file from storage
     */
    public function download($localPath);

    /**
     * Return a id for manipulation
     */
    public function getId();

    /**
     * Delete a file from storage
     */
    public function delete();

    /**
     * get its storage
     */
    public function getStorage();
}