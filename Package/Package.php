<?php

namespace Tee\Backup\Package;

use Illuminate\Support\Collection;

class Package extends Collection
{
    public $filename;
    public $md5;

    public function jsonSerialize() {
        return array(
            'items' => $this->all(),
            'md5' => $this->md5
        );
    }

    public function toJson($options=0) {
        return json_encode($this);
    }
}