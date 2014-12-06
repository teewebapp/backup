<?php

namespace Tee\Backup\Storage;

interface File
{
    public function getName();
    public function download($localPath);
}