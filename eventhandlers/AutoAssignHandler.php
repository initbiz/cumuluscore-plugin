<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\EventHandlers;

use Db;
use Event;
use RainLab\User\Models\User;
use RainLab\User\Models\UserGroup;
use Initbiz\CumulusCore\Models\Cluster;
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
        $event->listen('rainlab.user.register', function ($component, $user) {

            $data = $this->input;

            if (! AutoAssignSettings::get('enable_auto_assign_user')) {
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

                $cluster = new Cluster;
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

            try {
                $user->clusters()->syncWithoutDetaching([$cluster->id]);

                Event::fire('initbiz.cumuluscore.autoAssignUserToCluster', [$user, $cluster]);
            } catch (\Exception $e) {

                Db::rollback();

                if (env('APP_DEBUG', true)) {
                    throw $e;
                }

                trace_log($e);
                throw new \Exception('Error auto assigning user to cluster');
            }
        }, 100);
    }

    public function autoAssignUserToGroup($event)
    {
        $event->listen('rainlab.user.register', function ($component, $user) {
            if (! AutoAssignSettings::get('enable_auto_assign_user_to_group')) {
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
        $event->listen('rainlab.user.register', function ($component, $user) {
            // Place to test if everything went as expected

            $dbUser = User::find($user->id);
            if ($dbUser) {
                $state = Event::fire('initbiz.cumuluscore.registrationComplete', [$dbUser], true);
                if ($state === false) {
                    Db::rollback();
                    throw new ApplicationException('Registration failed');
                } else {
                    Db::commit();
                }
            }
        }, 10);
    }
}
