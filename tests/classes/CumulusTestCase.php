<?php

namespace Initbiz\CumulusCore\Tests\Classes;

use Schema;
use Storage;
use RainLab\User\Classes\AuthManager;
use Initbiz\InitDry\Tests\Classes\FullPluginTestCase;

class CumulusTestCase extends FullPluginTestCase
{
    /**
     * AuthManager used to login/logout the users
     *
     * @var AuthManager
     */
    protected $manager;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        Schema::create('initbiz_cumuluscore_encryptable_model', function ($table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('slug');
            $table->integer('cluster_id')->unsigned()->nullable();
            $table->timestamps();
        });

        app()->bind('user.auth', function () {
            return AuthManager::instance();
        });

        $this->manager = AuthManager::instance();
        $this->manager->logout();
    }
}
