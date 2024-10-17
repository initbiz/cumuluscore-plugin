<?php

namespace Initbiz\CumulusCore\Tests\Classes;

use Schema;
use Storage;
use Initbiz\InitDry\Tests\Classes\FullPluginTestCase;

class CumulusTestCase extends FullPluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        Schema::create('initbiz_cumuluscore_encryptable_model', function ($table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('slug');
            $table->string('confidential_field')->nullable();
            $table->integer('cluster_id')->unsigned()->nullable();
            $table->timestamps();
        });
    }
}
