<?php namespace Initbiz\CumulusCore\EventHandlers;

use Db;
use Event;
use RainLab\User\Models\User;
use RainLab\User\Models\UserGroup;
use Initbiz\CumulusCore\Models\Plan;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\CumulusCore\Models\AutoAssignSettings;

class AutoAssignHandler
{
    public function subscribe($event)
    {
        $this->beginTransactionBefore($event);
        $this->autoAssignUserCluster($event);
        $this->autoAssignUserToGroup($event);
        $this->commitTransactionOnSuccess($event);
    }

    public function beginTransactionBefore($event)
    {
        $event->listen('rainlab.user.beforeRegister', function (&$data) {
            Db::beginTransaction();
        }, 500);
    }

    public function autoAssignUserCluster($event)
    {
        $event->listen('rainlab.user.register', function ($user, $data) {
            if (!AutoAssignSettings::get('enable_auto_assign_user')) {
                return true;
            }

            Event::fire('initbiz.cumuluscore.beforeAutoAssignUserToCluster', [&$data]);

            if (AutoAssignSettings::get('auto_assign_user') === 'concrete_cluster') {
                $clusterSlug = AutoAssignSettings::get('auto_assign_user_concrete_cluster');
                $cluster = Cluster::where('slug', $clusterSlug)->first();
            }

            if (AutoAssignSettings::get('auto_assign_user') === 'get_cluster') {
                $clusterSlug = $data[AutoAssignSettings::get('auto_assign_user_get_cluster')];
                $cluster = Cluster::where('slug', $clusterSlug)->first();
            }

            if (AutoAssignSettings::get('auto_assign_user') === 'new_cluster') {
                $cluster = new Cluster();
                $cluster->name           = $data[AutoAssignSettings::get('auto_assign_user_new_cluster')];
                $cluster->thoroughfare   = (isset($data['thoroughfare']))   ? $data['thoroughfare']: null;
                $cluster->city           = (isset($data['city']))           ? $data['city']: null;
                $cluster->phone          = (isset($data['phone']))          ? $data['phone']: null;
                $cluster->country_id     = (isset($data['country_id']))     ? $data['country_id']: null;
                $cluster->postal_code    = (isset($data['postal_code']))    ? $data['postal_code']: null;
                $cluster->description    = (isset($data['description']))    ? $data['description']: null;
                $cluster->email          = (isset($data['cluster_email']))  ? $data['cluster_email']: null;
                $cluster->tax_number     = (isset($data['tax_number']))     ? $data['tax_number']: null;
                $cluster->account_number = (isset($data['account_number'])) ? $data['account_number']: null;

                $cluster->save();
            }

            try {
                $user->clusters()->syncWithoutDetaching($cluster);
                Event::fire('initbiz.cumuluscore.autoAssignUserToCluster', [$user, $cluster]);
            } catch (\Exception $e) {
                Db::rollback();
                if (env('APP_DEBUG', false)) {
                    throw $e;
                } else {
                    trace_log($e);
                    throw new \Exception("Error auto assigning user to cluster", 1);
                }
            }

            if (AutoAssignSettings::get('auto_assign_user') === 'new_cluster' && AutoAssignSettings::get('enable_auto_assign_cluster')) {
                Event::fire('initbiz.cumuluscore.beforeAutoAssignClusterToPlan', [&$data]);

                if (AutoAssignSettings::get('auto_assign_cluster') === 'get_plan') {
                    $planSlug = $data[AutoAssignSettings::get('auto_assign_cluster_get_plan')];
                    $plan = Plan::where('slug', $planSlug)->first();
                }

                if (AutoAssignSettings::get('auto_assign_cluster') === 'concrete_plan') {
                    $planSlug = AutoAssignSettings::get('auto_assign_cluster_concrete_plan');
                    $plan = Plan::where('slug', $planSlug)->first();
                }

                if ($plan->is_registration_allowed) {
                    $cluster->plan()->associate($plan);
                    $cluster->save();
                    Event::fire('initbiz.cumuluscore.autoAssignClusterToPlan', [$cluster, $plan]);
                } else {
                    Db::rollback();
                    if (env('APP_DEBUG', false)) {
                        throw $e;
                    } else {
                        trace_log($e);
                        throw new \Exception("Error auto assigning cluster to plan", 1);
                    }
                }
            }
        }, 100);
    }

    public function autoAssignUserToGroup($event)
    {
        $event->listen('rainlab.user.register', function ($user, $data) {
            if (!AutoAssignSettings::get('enable_auto_assign_user_to_group')) {
                return true;
            }

            $group = UserGroup::where('code', AutoAssignSettings::get('group_to_auto_assign_user'))->first();
            if ($group) {
                $user->groups()->add($group);
            }
        }, 90);
    }

    public function commitTransactionOnSuccess($event)
    {
        $event->listen('rainlab.user.register', function ($user, $data) {
            // Place to test if everything went as expected
            
            $dbUser = User::find($user->id);
            if ($dbUser) {
                Db::commit();
            }
        }, 1);
    }
}



