<?php

namespace Initbiz\CumulusCore\Tests\Models;

use Storage;
use Initbiz\CumulusCore\Models\Plan;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\Cumuluscore\Models\ClusterFeatureLog;
use Initbiz\InitDry\Tests\Classes\FullPluginTestCase;

class ClusterTest extends FullPluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function testCanEnterFeature()
    {
        $cluster = new Cluster;
        $cluster->name = 'Company';
        $cluster->slug= 'company';
        $cluster->save();

        $plan = new Plan;
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
        $cluster = new Cluster;
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $plan = new Plan;
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
        $cluster = new Cluster;
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $plan = new Plan;
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
        $this->assertFalse($cluster->canEnterAnyFeature(['initbiz.cumulusdemo.advanced.todo', 'initbiz.cumulusdemo.basic.dashboard']));

        $cluster->plan()->add($plan);

        $this->assertTrue($cluster->canEnterAnyFeature(['initbiz.cumulusdemo.advanced.todo', 'initbiz.cumulusdemo.basic.dashboard']));
        $this->assertTrue($cluster->canEnterAnyFeature(['initbiz.cumulusdemo.advanced.todo', 'initbiz.cumulusdemo.basic.todo']));
        $this->assertFalse($cluster->canEnterAnyFeature(['initbiz.cumulusdemo.basic.todo', 'initbiz.cumulusdemo.basic.todo']));
        $this->assertFalse($cluster->canEnterAnyFeature([]));
    }

    public function testFeatures()
    {
        $cluster = new Cluster;
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $plan = new Plan;
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
        $cluster = new Cluster;
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $plan = new Plan;
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
        $cluster = new Cluster;
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $plan = new Plan;
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
        $cluster = new Cluster;
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $cluster->registerFeature('initbiz.cumulusdemo.basic.dashboard');
        $log = ClusterFeatureLog::where('cluster_slug', 'company')->where('feature_code', 'initbiz.cumulusdemo.basic.dashboard')->first();

        $this->assertEquals('registered', $log->action);
    }

    public function testDeregisterFeature()
    {
        $cluster = new Cluster;
        $cluster->name = 'Company';
        $cluster->slug = 'company';
        $cluster->save();

        $cluster->registerFeature('initbiz.cumulusdemo.basic.dashboard');
        $log = ClusterFeatureLog::where('cluster_slug', 'company')->where('feature_code', 'initbiz.cumulusdemo.basic.dashboard')->first();

        $this->assertEquals('registered', $log->action);
        sleep(1);

        $cluster->deregisterFeature('initbiz.cumulusdemo.basic.dashboard');
        $log = ClusterFeatureLog::where('cluster_slug', 'company')->where('feature_code', 'initbiz.cumulusdemo.basic.dashboard')->orderBy('timestamp', 'desc')->first();

        $this->assertEquals('deregistered', $log->action);
    }
}
