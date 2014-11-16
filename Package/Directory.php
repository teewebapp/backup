<?php
namespace Tee\Backup\Package;

use Illuminate\Support\Contracts\JsonableInterface;

class Directory implements \JsonSerializable, JsonableInterface {
    public $directory;
    public $filename;
    public $md5;

    public function jsonSerialize() {
        return array(
            'directory' => $this->directory,
            'filename' => $this->filename,
            'md5' => $this->md5
        );
    }

    public function toJson($options=0) {
        return json_encode($this);
    }
}