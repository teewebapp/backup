<?php
namespace Tee\Backup\Package;

use Illuminate\Support\Contracts\JsonableInterface;

class Database implements \JsonSerializable, JsonableInterface {
    public $connection;
    public $filename;
    public $md5;

    public function jsonSerialize() {
        return array(
            'connection' => $this->connection,
            'filename' => $this->filename,
            'md5' => $this->md5
        );
    }

    public function toJson($options=0) {
        return json_encode($this);
    }
}