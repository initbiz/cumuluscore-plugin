<?php

namespace Initbiz\CumulusCore\Tests\Models;

use RainLab\User\Models\User;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\CumulusCore\Tests\Classes\CumulusTestCase;
use Initbiz\CumulusCore\Tests\Classes\EncryptableModel;
use Initbiz\CumulusCore\Classes\Exceptions\CannotUseClusterEncrypterException;

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
        $user->password_confirmation = 'test12345';
        $user->is_activated = 1;
        $user->save();
        $user->clusters()->add($cluster);

        $encryptableModel = new EncryptableModel();
        $encryptableModel->name = 'Company';
        $encryptableModel->slug = 'company';
        $encryptableModel->save();
        $encryptableModel->cluster()->add($cluster);

        $this->manager->login($user);
        Helpers::setCluster($cluster);

        $encryptableModel->confidential_field = 'Confidential string';
        $encryptableModel->save();

        $this->assertEquals($encryptableModel->confidential_field, 'Confidential string');

        $this->manager->logout($user);

        $record = \Db::table('initbiz_cumuluscore_encryptable_model')->first();
        $this->assertNotEmpty($record->confidential_field);
        $this->assertNotEquals($record->confidential_field, 'Confidential string');

        $this->expectException(CannotUseClusterEncrypterException::class);
        $encryptableModel->confidential_field = 'Confidential string';
        $encryptableModel->save();
    }
}
