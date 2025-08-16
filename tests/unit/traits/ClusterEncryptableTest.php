<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Tests\Models;

use Auth;
use RainLab\User\Models\User;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\CumulusCore\Tests\Classes\CumulusTestCase;
use Initbiz\CumulusCore\Tests\Classes\EncryptableModel;

class ClusterEncryptableTest extends CumulusTestCase
{
    public function testEncryptAttribute()
    {
        $cluster = new Cluster();
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $user = new User();
        // RainLab.User v2 compatibility
        if (\Schema::hasColumn('users', 'first_name')) {
            $user->first_name = 'test';
        } else {
            $user->name = 'test';
            $user->surname = 'test';
        }
        $user->email = 'test@test.com';
        $user->password = 'test12345';
        $user->password_confirmation = 'test12345';
        $user->is_activated = true;
        $user->save();
        $user->clusters()->add($cluster);

        $encryptableModel = new EncryptableModel();
        $encryptableModel->name = 'Company';
        $encryptableModel->slug = 'company';
        $encryptableModel->save();
        $encryptableModel->cluster()->add($cluster);

        Auth::login($user);
        Helpers::setCluster($cluster);

        $encryptableModel->confidential_field = 'Confidential string';
        $encryptableModel->save();

        $this->assertEquals($encryptableModel->confidential_field, 'Confidential string');

        Auth::logout($user);

        $record = \Db::table('initbiz_cumuluscore_encryptable_model')->first();
        $this->assertNotEmpty($record->confidential_field);
        $this->assertNotEquals($record->confidential_field, 'Confidential string');
    }
}
