<?php

namespace Initbiz\CumulusCore\Tests\Classes;

use Storage;
use Initbiz\InitDry\Tests\Classes\FullPluginTestCase;

class CumulusTestCase extends FullPluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }
}
