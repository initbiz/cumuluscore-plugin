<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\EventHandlers;

use Db;
use Lang;
use Event;
use RainLab\User\Models\User;
use RainLab\User\Models\UserGroup;
use Initbiz\CumulusCore\Models\Plan;
use Initbiz\CumulusCore\Models\Cluster;
use RainLab\User\Components\Registration;
use October\Rain\Exception\SystemException;
use October\Rain\Exception\ApplicationException;
use Initbiz\CumulusCore\Models\AutoAssignSettings;

class AutoAssignHandler
{
    protected $input;

    public function subscribe($event)
    {
        $this->beginTransactionBefore($event);
        $this->storeInput($event);
        $this->autoAssignUserCluster($event);
        $this->autoAssignUserToGroup($event);
        $this->commitTransactionOnSuccess($event);
    }

    public function beginTransactionBefore($event)
    {
        $event->listen('rainlab.user.beforeRegister', function (&$data) {
            Db::beginTransaction();
        }, 1000);
    }

    public function storeInput($event)
    {
        $event->listen('rainlab.user.beforeRegister', function ($component, &$input) {
            $this->input = $input;
        }, 900);
    }

    public function autoAssignUserCluster($event)
    {
        $event->listen('rainlab.user.register', function ($param1, $param2) {
            try {
                if ($param1 instanceof Registration) {
                    $data = $this->input;
                    $user = $param2;
                } else {
                    $user = $param1;
                    $data = $param2;
                }

                if (!AutoAssignSettings::get('enable_auto_assign_user')) {
                    return;
                }

                Event::fire('initbiz.cumuluscore.beforeAutoAssignUserToCluster', [&$data]);

                $cluster = null;

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
                    $cluster->name = $data[AutoAssignSettings::get('auto_assign_user_new_cluster')];
                    $cluster->thoroughfare = $data['thoroughfare'] ?? null;
                    $cluster->city = $data['city'] ?? null;
                    $cluster->phone = $data['phone'] ?? null;
                    $cluster->country_id = $data['country_id'] ?? null;
                    $cluster->postal_code = $data['postal_code'] ?? null;
                    $cluster->description = $data['description'] ?? null;
                    $cluster->email = $data['cluster_email'] ?? null;
                    $cluster->tax_number = $data['tax_number'] ?? null;
                    $cluster->account_number = $data['account_number'] ?? null;

                    Event::fire('initbiz.cumuluscore.autoAssignBeforeClusterCreate', [&$cluster, $data]);

                    $cluster->save();

                    Event::fire('initbiz.cumuluscore.autoAssignAfterClusterCreate', [$cluster]);
                }

                if (empty($cluster)) {
                    throw new SystemException('Could not find a cluster to auto assign the user to');
                }

                $user->clusters()->syncWithoutDetaching([$cluster->id]);

                Event::fire('initbiz.cumuluscore.autoAssignUserToCluster', [$user, $cluster]);

                if (
                    AutoAssignSettings::get('auto_assign_user') === 'new_cluster'
                    && AutoAssignSettings::get('enable_auto_assign_cluster')
                ) {
                    Event::fire('initbiz.cumuluscore.beforeAutoAssignClusterToPlan', [&$data]);

                    $plan = null;

                    if (AutoAssignSettings::get('auto_assign_cluster') === 'get_plan') {
                        $planSlug = $data[AutoAssignSettings::get('auto_assign_cluster_get_plan')];
                        $plan = Plan::where('slug', $planSlug)->first();
                    }

                    if (AutoAssignSettings::get('auto_assign_cluster') === 'concrete_plan') {
                        $planSlug = AutoAssignSettings::get('auto_assign_cluster_concrete_plan');
                        $plan = Plan::where('slug', $planSlug)->first();
                    }

                    if (empty($plan)) {
                        throw new SystemException('Could not find a plan to auto assign the cluster to');
                    }

                    if (!$plan->is_registration_allowed) {
                        throw new SystemException('Registration is not allowed for the plan ' . $plan->slug);
                    }

                    $cluster->plan()->associate($plan);
                    $cluster->save();

                    Event::fire('initbiz.cumuluscore.autoAssignClusterToPlan', [$cluster, $plan]);
                }
            } catch (\Throwable $e) {
                // Roll back the transaction started in beforeRegister, rethrowing keeps the exception in logs
                Db::rollBack();
                throw $e;
            }
        }, 100);
    }

    public function autoAssignUserToGroup($event)
    {
        $event->listen('rainlab.user.register', function ($param1, $param2) {
            try {
                if ($param1 instanceof Registration) {
                    $user = $param2;
                } else {
                    $user = $param1;
                }

                if (!AutoAssignSettings::get('enable_auto_assign_user_to_group')) {
                    return;
                }

                $group = UserGroup::where('code', AutoAssignSettings::get('group_to_auto_assign_user'))->first();
                if ($group) {
                    $user->groups()->add($group);
                }
            } catch (\Throwable $e) {
                // Roll back the transaction started in beforeRegister, rethrowing keeps the exception in logs
                Db::rollBack();
                throw $e;
            }
        }, 90);
    }

    public function commitTransactionOnSuccess($event)
    {
        $event->listen('rainlab.user.register', function ($param1, $param2) {
            try {
                if ($param1 instanceof Registration) {
                    $user = $param2;
                } else {
                    $user = $param1;
                }

                $dbUser = User::find($user->id);
                if (empty($dbUser)) {
                    throw new SystemException('Registered user ' . $user->id . ' was not found in the database');
                }

                // Place to test if everything went as expected
                $state = Event::fire('initbiz.cumuluscore.registrationComplete', [$dbUser], true);
                if ($state === false) {
                    throw new ApplicationException(Lang::get('initbiz.cumuluscore::lang.auto_assign.registration_failed'));
                }
            } catch (\Throwable $e) {
                // Roll back the transaction started in beforeRegister, rethrowing keeps the exception in logs
                Db::rollBack();
                throw $e;
            }

            Db::commit();
        }, 10);
    }
}
