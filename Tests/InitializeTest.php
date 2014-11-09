<?php

namespace Tee\Backup\Tests;

use Tee\System\Tests\TestCase;

class InitializeTest extends TestCase {

    public function testInitialized()
    {
        $this->assertTrue(\moduleEnabled('system'));
    }

}