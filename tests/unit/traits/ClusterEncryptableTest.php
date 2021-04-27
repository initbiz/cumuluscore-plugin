<?php

namespace Initbiz\CumulusCore\Tests\Models;

use Initbiz\CumulusCore\Classes\Exceptions\CannotUseClusterEncrypterException;
use RainLab\User\Models\User;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\CumulusCore\Tests\Classes\CumulusTestCase;
use Initbiz\CumulusCore\Tests\Classes\EncryptableModel;

class ClusterEncryptableTest extends CumulusTestCase
{
    public function testEncryptAttribute()
    {
        $cluster = new Cluster();
        $cluster->name = 'Company';
        $cluster->slug= 'company';
        $cluster->save();

        $user = new User();
        $user->name = 'test';
        $user->email = 'test@test.com';
        $user->password = 'test12345';
        $user->is_activated = 1;
        $user->save();

        $encryptableModel = new EncryptableModel();
        $encryptableModel->name = 'Company';
        $encryptableModel->slug= 'company';
        $encryptableModel->save();
        $encryptableModel->cluster()->add($cluster);
        $encryptableModel->save();

        $this->manager->login($user);

        $encryptableModel->confidential_field = 'Confidential string';
        $encryptableModel->save();

        $this->assertEquals($encryptableModel->confidential_field, 'Confidential string');

        $this->manager->logout($user);
        $this->assertEquals($encryptableModel->confidential_field, 'Confidential string');

        $this->expectException(CannotUseClusterEncrypterException::class);
        $encryptableModel->confidential_field= 'Confidential string';
        $encryptableModel->save();
    }
}

