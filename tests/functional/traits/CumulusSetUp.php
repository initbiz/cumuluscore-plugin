<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Tests\Traits;

use Initbiz\CumulusCore\Models\Plan;
use Initbiz\CumulusCore\Models\Cluster;

trait CumulusSetUp
{
    public $trialPlan;

    public $freePlan;

    public $plusPlan;

    public $proPlan;

    public $freeCluster;

    public $plusCluster;

    public $proCluster;

    public function cumulusSetUp()
    {
        $trialPlan = new Plan();
        $trialPlan->name = 'Trial';
        $trialPlan->slug = 'trial';
        $trialPlan->is_registration_allowed = true;
        $trialPlan->is_trial = true;
        $trialPlan->save();

        $freePlan = new Plan();
        $freePlan->name = 'Free';
        $freePlan->slug = 'free';
        $freePlan->is_registration_allowed = true;
        $freePlan->is_trial = false;
        $freePlan->save();

        $plusPlan = new Plan();
        $plusPlan->name = 'Plus';
        $plusPlan->slug = 'plus';
        $plusPlan->is_registration_allowed = false;
        $plusPlan->is_trial = false;
        $plusPlan->save();

        $proPlan = new Plan();
        $proPlan->name = 'Pro';
        $proPlan->slug = 'pro';
        $proPlan->is_registration_allowed = false;
        $proPlan->is_trial = false;
        $proPlan->save();

        $trialPlan->related_plans()->add($plusPlan, ['relation' => 'upgrade']);
        $trialPlan->related_plans()->add($proPlan, ['relation' => 'upgrade']);
        $freePlan->related_plans()->add($plusPlan, ['relation' => 'upgrade']);
        $freePlan->related_plans()->add($proPlan, ['relation' => 'upgrade']);
        $plusPlan->related_plans()->add($proPlan, ['relation' => 'upgrade']);

        $freeCluster = new Cluster();
        $freeCluster->name = 'Free cluster';
        $freeCluster->slug = 'free-cluster';
        $freeCluster->plan_id = $freePlan->id;
        $freeCluster->username = 'free-cluster';
        $freeCluster->save();

        $plusCluster = new Cluster();
        $plusCluster->name = 'Plus cluster';
        $plusCluster->slug = 'plus-cluster';
        $plusCluster->plan_id = $plusPlan->id;
        $plusCluster->username = 'plus-cluster';
        $plusCluster->save();

        $proCluster = new Cluster();
        $proCluster->name = 'Pro cluster';
        $proCluster->slug = 'pro-cluster';
        $proCluster->plan_id = $proPlan->id;
        $proCluster->username = 'pro-cluster';
        $proCluster->save();

        $this->trialPlan = $trialPlan;
        $this->freePlan = $freePlan;
        $this->plusPlan = $plusPlan;
        $this->proPlan = $proPlan;

        $this->freeCluster = $freeCluster;
        $this->plusCluster = $plusCluster;
        $this->proCluster = $proCluster;
    }
}
