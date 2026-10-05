<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Tests\Classes;

use Schema;
use Storage;
use PluginTestCase;

class CumulusTestCase extends PluginTestCase
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
