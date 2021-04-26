<?php

namespace Initbiz\CumulusCore\Tests\Models;

use RainLab\User\Models\User;
use Initbiz\CumulusCore\Models\Plan;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\InitDry\Tests\Classes\FullPluginTestCase;

class PlanTest extends FullPluginTestCase
{
    public function testPlansToUpgrade()
    {
        $firstPlan = new Plan();
        $firstPlan->name = 'test1';
        $firstPlan->slug = 'test1';
        $firstPlan->save();
        
        $secondPlan = new Plan();
        $secondPlan->name = 'test2';
        $secondPlan->slug = 'test2';
        $secondPlan->save();

        $thirdPlan = new Plan();
        $thirdPlan->name = 'test3';
        $thirdPlan->slug = 'test3';
        $thirdPlan->save();

        $this->assertEquals(0, $firstPlan->plansToUpgrade()->count());

        $firstPlan->related_plans()->add($secondPlan, ['relation' => 'upgrade']);

        $this->assertEquals(1, $firstPlan->plansToUpgrade()->count());

        $firstPlan->related_plans()->add($thirdPlan, ['relation' => 'upgrade']);

        $this->assertEquals(2, $firstPlan->plansToUpgrade()->count());
    }

    public function testCanUpgrade()
    {
        $firstPlan = new Plan();
        $firstPlan->name = 'test1';
        $firstPlan->slug = 'test1';
        $firstPlan->save();
        
        $secondPlan = new Plan();
        $secondPlan->name = 'test2';
        $secondPlan->slug = 'test2';
        $secondPlan->save();

        $thirdPlan = new Plan();
        $thirdPlan->name = 'test3';
        $thirdPlan->slug = 'test3';
        $thirdPlan->save();

        $this->assertEquals(false, $firstPlan->canUpgrade());

        $firstPlan->related_plans()->add($secondPlan, ['relation' => 'upgrade']);

        $this->assertEquals(true, $firstPlan->canUpgrade());

        $firstPlan->related_plans()->add($thirdPlan, ['relation' => 'upgrade']);

        $this->assertEquals(true, $firstPlan->canUpgrade());
    }

    public function testCanUpgradeTo()
    {
        $firstPlan = new Plan();
        $firstPlan->name = 'test1';
        $firstPlan->slug = 'test1';
        $firstPlan->save();
        
        $secondPlan = new Plan();
        $secondPlan->name = 'test2';
        $secondPlan->slug = 'test2';
        $secondPlan->save();

        $thirdPlan = new Plan();
        $thirdPlan->name = 'test3';
        $thirdPlan->slug = 'test3';
        $thirdPlan->save();

        $this->assertFalse($firstPlan->canUpgradeTo($secondPlan));
        $this->assertTrue($firstPlan->canUpgradeTo($thirdPlan));

        $firstPlan->related_plans()->add($secondPlan, ['relation' => 'upgrade']);

        $this->assertTrue($firstPlan->canUpgradeTo($secondPlan));
        $this->assertFalse($firstPlan->canUpgradeTo($thirdPlan));

        $firstPlan->related_plans()->add($thirdPlan, ['relation' => 'upgrade']);

        $this->assertTrue($firstPlan->canUpgradeTo($secondPlan));
        $this->assertTrue($firstPlan->canUpgradeTo($thirdPlan));
    }

    public function testCanProlongate()
    {
        $firstPlan = new Plan();
        $firstPlan->name = 'test1';
        $firstPlan->slug = 'test1';
        $firstPlan->save();

        $secondPlan = new Plan();
        $secondPlan->name = 'test2';
        $secondPlan->slug = 'test2';
        $secondPlan->is_expiring = 1;
        $secondPlan->is_trial = 1;
        $secondPlan->save();

        $thirdPlan = new Plan();
        $thirdPlan->name = 'test3';
        $thirdPlan->slug = 'test3';
        $thirdPlan->is_expiring = 1;
        $thirdPlan->is_trial = 0;
        $thirdPlan->save();

        $this->assertFalse($firstPlan->canProlongate());
        $this->assertFalse($secondPlan->canProlongate());
        $this->assertTrue($thirdPlan->canProlongate());
    }

    public function testGetUsersAttribute()
    {
        $firstUser = new User();
        $firstUser->email = 'first@user.com';
        $firstUser->password = 'first@user.com';
        $firstUser->save();

        $secondUser = new User();
        $secondUser->email = 'second@user.com';
        $secondUser->password = 'second@user.com';
        $secondUser->save();

        $firstPlan = new Plan();
        $firstPlan->name = 'test1';
        $firstPlan->slug = 'test1';
        $firstPlan->save();

        $firstCluster = new Cluster();
        $firstCluster->name = 'cluster1';
        $firstCluster->slug = 'cluster1';
        $firstCluster->save();

        $secondCluster = new Cluster();
        $secondCluster->name = 'cluster2';
        $secondCluster->slug = 'cluster2';
        $secondCluster->save();

        $firstUser->clusters()->add($firstCluster);
        $secondUser->clusters()->add($firstCluster);
        $secondUser->clusters()->add($secondCluster);
        
        $firstPlan->clusters()->add($firstCluster);
        $firstPlan->clusters()->add($secondCluster);

        $this->assertEquals(3, $firstPlan->users->count());
    }
}
