<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Tests\Unit\Models;

use Auth;
use Cookie;
use Session;
use RainLab\User\Models\User;
use Initbiz\CumulusCore\Models\Plan;
use Illuminate\Support\Facades\Event;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\CumulusCore\Classes\ClusterKey;
use Initbiz\Cumuluscore\Models\ClusterFeatureLog;
use Initbiz\CumulusCore\Tests\Classes\CumulusTestCase;

class ClusterTest extends CumulusTestCase
{
    public function testCanEnterFeature()
    {
        $cluster = new Cluster();
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $plan = new Plan();
        $plan->name = 'test plan';
        $plan->slug = 'test-plan';
        $plan->features = [
            'initbiz.cumulusdemo.basic.gallery',
        ];
        $plan->save();

        $this->assertFalse($cluster->canEnterFeature('initbiz.cumulusdemo.basic.gallery'));
        $this->assertFalse($cluster->canEnterFeature('initbiz.cumulusdemo.basic.todo'));

        $cluster->plan()->add($plan);

        $this->assertTrue($cluster->canEnterFeature('initbiz.cumulusdemo.basic.gallery'));
        $this->assertFalse($cluster->canEnterFeature('initbiz.cumulusdemo.basic.todo'));
    }

    public function testHasFeature()
    {
        $cluster = new Cluster();
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $plan = new Plan();
        $plan->name = 'test plan';
        $plan->slug = 'test-plan';
        $plan->features = [
            'initbiz.cumulusdemo.basic.gallery',
        ];
        $plan->save();

        $this->assertFalse($cluster->hasFeature('initbiz.cumulusdemo.basic.gallery'));
        $this->assertFalse($cluster->hasFeature('initbiz.cumulusdemo.basic.todo'));

        $cluster->plan()->add($plan);

        $this->assertTrue($cluster->hasFeature('initbiz.cumulusdemo.basic.gallery'));
        $this->assertFalse($cluster->hasFeature('initbiz.cumulusdemo.basic.todo'));
    }

    public function testCanEnterAnyFeature()
    {
        $cluster = new Cluster();
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $plan = new Plan();
        $plan->name = 'test plan';
        $plan->slug = 'test-plan';
        $plan->features = [
            'initbiz.cumulusdemo.advanced.dashboard',
            'initbiz.cumulusdemo.advanced.todo',
            'initbiz.cumulusdemo.advanced.gallery',
            'initbiz.cumulusdemo.basic.dashboard',
            'initbiz.cumulussubscriptions.manage_subscription',
            'initbiz.cumulusplus.cluster_settings',
        ];
        $plan->save();

        $this->assertFalse($cluster->canEnterAnyFeature([]));
        $this->assertFalse($cluster->canEnterAnyFeature([
            'initbiz.cumulusdemo.advanced.todo',
            'initbiz.cumulusdemo.basic.dashboard'
        ]));

        $cluster->plan()->add($plan);

        $this->assertTrue($cluster->canEnterAnyFeature([
            'initbiz.cumulusdemo.advanced.todo',
            'initbiz.cumulusdemo.basic.dashboard'
        ]));
        $this->assertTrue($cluster->canEnterAnyFeature([
            'initbiz.cumulusdemo.advanced.todo',
            'initbiz.cumulusdemo.basic.todo'
        ]));
        $this->assertFalse($cluster->canEnterAnyFeature([
            'initbiz.cumulusdemo.basic.todo',
            'initbiz.cumulusdemo.basic.todo'
        ]));
        $this->assertFalse($cluster->canEnterAnyFeature([]));
    }

    public function testFeatures()
    {
        $cluster = new Cluster();
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $plan = new Plan();
        $plan->name = 'test plan';
        $plan->slug = 'test-plan';
        $plan->features = [
            'initbiz.cumulusdemo.advanced.dashboard',
            'initbiz.cumulusdemo.advanced.todo',
            'initbiz.cumulusdemo.advanced.gallery',
            'initbiz.cumulusdemo.basic.dashboard',
            'initbiz.cumulussubscriptions.manage_subscription',
            'initbiz.cumulusplus.cluster_settings',
        ];
        $plan->save();

        $this->assertNotEquals([
            'initbiz.cumulusdemo.advanced.dashboard',
            'initbiz.cumulusdemo.advanced.todo',
            'initbiz.cumulusdemo.advanced.gallery',
            'initbiz.cumulusdemo.basic.dashboard',
            'initbiz.cumulussubscriptions.manage_subscription',
            'initbiz.cumulusplus.cluster_settings',
        ], $cluster->features);
        $this->assertEquals([], $cluster->features);

        $cluster->plan()->add($plan);

        $this->assertEquals([
            'initbiz.cumulusdemo.advanced.dashboard',
            'initbiz.cumulusdemo.advanced.todo',
            'initbiz.cumulusdemo.advanced.gallery',
            'initbiz.cumulusdemo.basic.dashboard',
            'initbiz.cumulussubscriptions.manage_subscription',
            'initbiz.cumulusplus.cluster_settings',
        ], $cluster->features);
        $this->assertNotEquals([], $cluster->features);
    }

    public function testRegisteredFeatures()
    {
        $cluster = new Cluster();
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $plan = new Plan();
        $plan->name = 'test plan';
        $plan->slug = 'test-plan';
        $plan->features = [
            'initbiz.cumulusdemo.advanced.dashboard',
            'initbiz.cumulusdemo.advanced.todo',
            'initbiz.cumulusdemo.advanced.gallery',
            'initbiz.cumulusdemo.basic.dashboard',
            'initbiz.cumulussubscriptions.manage_subscription',
            'initbiz.cumulusplus.cluster_settings',
        ];
        $plan->save();

        $cluster->plan()->add($plan);
        $cluster->save();

        $this->assertEquals([
            'initbiz.cumulusdemo.advanced.dashboard',
            'initbiz.cumulusdemo.advanced.todo',
            'initbiz.cumulusdemo.advanced.gallery',
            'initbiz.cumulusdemo.basic.dashboard',
            'initbiz.cumulussubscriptions.manage_subscription',
            'initbiz.cumulusplus.cluster_settings',
        ], $cluster->registered_features);
        $this->assertNotEquals([], $cluster->registered_features);

        // For registering methods which takes the latest element order desc by timestamp
        sleep(1);
        $plan->features = ['initbiz.cumulusdemo.basic.dashboard'];
        $plan->save();
        $cluster->save();

        $this->assertNotEquals([], $cluster->registered_features);
        $this->assertNotEquals('initbiz.cumulusdemo.basic.dashboard', $cluster->registered_features);
        $this->assertNotEquals([
            'initbiz.cumulusdemo.advanced.dashboard',
            'initbiz.cumulusdemo.advanced.todo',
            'initbiz.cumulusdemo.advanced.gallery',
            'initbiz.cumulusdemo.basic.dashboard',
            'initbiz.cumulussubscriptions.manage_subscription',
            'initbiz.cumulusplus.cluster_settings',
        ], $cluster->registered_features);
        $this->assertEquals(['initbiz.cumulusdemo.basic.dashboard'], $cluster->registered_features);
    }

    public function testRefreshRegisteredFunctions()
    {
        $cluster = new Cluster();
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $plan = new Plan();
        $plan->name = 'test plan';
        $plan->slug = 'test-plan';
        $plan->features = [
            'initbiz.cumulusdemo.advanced.dashboard',
            'initbiz.cumulusdemo.advanced.todo',
            'initbiz.cumulusdemo.advanced.gallery',
            'initbiz.cumulusdemo.basic.dashboard',
            'initbiz.cumulussubscriptions.manage_subscription',
            'initbiz.cumulusplus.cluster_settings',
        ];
        $plan->save();

        $cluster->plan()->add($plan);
        $cluster->save();

        $this->assertEquals([
            'initbiz.cumulusdemo.advanced.dashboard',
            'initbiz.cumulusdemo.advanced.todo',
            'initbiz.cumulusdemo.advanced.gallery',
            'initbiz.cumulusdemo.basic.dashboard',
            'initbiz.cumulussubscriptions.manage_subscription',
            'initbiz.cumulusplus.cluster_settings',
        ], $cluster->registered_features);
        $this->assertNotEquals([], $cluster->registered_features);

        // For registering methods which takes the latest element order desc by timestamp
        sleep(1);
        $cluster->refreshRegisteredFeatures(['initbiz.cumulusdemo.basic.dashboard']);

        $this->assertNotEquals([
            'initbiz.cumulusdemo.advanced.dashboard',
            'initbiz.cumulusdemo.advanced.todo',
            'initbiz.cumulusdemo.advanced.gallery',
            'initbiz.cumulusdemo.basic.dashboard',
            'initbiz.cumulussubscriptions.manage_subscription',
            'initbiz.cumulusplus.cluster_settings',
        ], $cluster->registered_features);
        $this->assertEquals(['initbiz.cumulusdemo.basic.dashboard'], $cluster->registered_features);
    }

    public function testRegisterFeature()
    {
        Event::fake();
        $cluster = new Cluster();
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $cluster->registerFeature('initbiz.cumulusdemo.basic.dashboard');
        $log = ClusterFeatureLog::where('cluster_id', $cluster->id)
            ->where('feature_code', 'initbiz.cumulusdemo.basic.dashboard')->first();

        $this->assertEquals('registered', $log->action);
    }

    public function testDeregisterFeature()
    {
        Event::fake();
        $cluster = new Cluster();
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $cluster->registerFeature('initbiz.cumulusdemo.basic.dashboard');
        $log = ClusterFeatureLog::where('cluster_id', $cluster->id)
            ->where('feature_code', 'initbiz.cumulusdemo.basic.dashboard')->first();

        $this->assertEquals('registered', $log->action);
        sleep(1);
        $cluster->deregisterFeature('initbiz.cumulusdemo.basic.dashboard');
        $log = ClusterFeatureLog::where('cluster_id', $cluster->id)
            ->where('feature_code', 'initbiz.cumulusdemo.basic.dashboard')
            ->orderBy('timestamp', 'desc')->first();

        $this->assertEquals('deregistered', $log->action);
    }

    public function testForgetCluster()
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

        Auth::login($user);
        Helpers::setCluster($cluster);
        $cluster = Helpers::getCluster();
        $this->assertNotNull($cluster);

        Auth::logout();
        $cluster = Helpers::getCluster();
        $this->assertNull($cluster);
        $this->assertNull(Cookie::get('cumulus_clusterslug'));
        $this->assertNull(Session::get('cumulus_clusterslug'));
    }

    public function testKeyEvents()
    {
        $cluster = new Cluster();
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $key = ClusterKey::get($cluster->slug);
        $this->assertNotEmpty($key);

        $cluster->delete();
        $this->assertEmpty(ClusterKey::get($cluster->slug));

        $cluster->restore();
        $this->assertEquals($key, ClusterKey::get($cluster->slug));
    }

    public function testScopeGetWithAccessToFeature()
    {
        $cluster = new Cluster();
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $plan = new Plan();
        $plan->name = 'test plan';
        $plan->slug = 'test-plan';
        $plan->features = [
            'initbiz.cumulusdemo.advanced.dashboard',
            'initbiz.cumulusdemo.advanced.todo',
        ];
        $plan->save();

        $cluster->plan()->add($plan);
        $cluster->save();

        $cluster2 = new Cluster();
        $cluster2->name = 'Company';
        $cluster2->slug = 'company';
        $cluster2->save();

        $plan2 = new Plan();
        $plan2->name = 'test plan';
        $plan2->slug = 'test-plan';
        $plan2->features = [
            'initbiz.cumulusdemo.advanced.dashboard',
        ];
        $plan2->save();

        $cluster2->plan()->add($plan2);
        $cluster2->save();

        $this->assertEquals(2, Cluster::withAccessToFeature('initbiz.cumulusdemo.advanced.dashboard')->count());
        $this->assertEquals(1, Cluster::withAccessToFeature('initbiz.cumulusdemo.advanced.todo')->count());
    }
}
