<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\EventHandlers;

use Lang;
use Redirect;
use RainLab\User\Models\User;
use Illuminate\Auth\Events\Logout;
use RainLab\User\Controllers\Users;
use RainLab\User\Components\Account;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\CumulusCore\Classes\Helpers;

class RainlabUserHandler
{
    public function subscribe($event)
    {
        $this->addOnRegirectMeAjaxHandler($event);
        $this->addClusterRelation($event);
        // $this->addClusterField($event);
        $this->addMethodsToUser($event);
        $this->addFullNameColumn($event);
        $this->forgetClusterOnLogout($event);
    }

    public function addOnRegirectMeAjaxHandler($event)
    {
        Account::extend(function ($component) {
            $component->addDynamicMethod('onRedirectMe', function () use ($component) {
                return Redirect::to($component->pageUrl($component->property('redirect')));
            });
        });
    }

    public function addClusterRelation($event)
    {
        User::extend(function ($model) {
            $model->belongsToMany['clusters'] = [
                Cluster::class,
                'table' => 'initbiz_cumuluscore_cluster_user',
                'order' => 'name',
                'key'      => 'user_id',
                'otherKey' => 'cluster_id'
            ];
        });
    }

    public function addMethodsToUser($event)
    {
        User::extend(function ($model) {
            $model->addDynamicMethod('scopeActivated', function ($query) use ($model) {
                return $query->where("is_activated", true);
            });

            $model->addDynamicMethod('scopeApplyTrashedFilter', function ($query, $type) use ($model) {
                switch ($type) {
                    case '1':
                        return $query->withTrashed();
                    case '2':
                        return $query->onlyTrashed();
                    default:
                        return $query;
                }
            });

            $model->addDynamicMethod('canEnter', function ($cluster) use ($model) {
                return $model->clusters->firstWhere('slug', $cluster->slug) ? true : false;
            });

            $model->addDynamicMethod('getFullNameAttribute', function ($user) use ($model) {
                return $model->name . ' ' . $model->surname;
            });

            $model->addDynamicMethod('getClusters', function () use ($model) {
                return $model->clusters;
            });
        });
    }

    public function addFullNameColumn($event)
    {
        $event->listen('backend.list.extendColumns', function ($widget) {
            if ($widget->getController() instanceof Users && $widget->model instanceof User) {
                $widget->removeColumn('name');
                $widget->addColumns([
                    'full_name' => [
                        'label' => Lang::get('initbiz.cumuluscore::lang.users.last_first_name')
                    ]
                ]);
            }
        });
    }

    public function forgetClusterOnLogout($event)
    {
        $event->listen('rainlab.user.logout', function ($user) {
            Helpers::forgetCluster();
        }, 100);

        $event->listen(Logout::class, function ($user) {
            Helpers::forgetCluster();
        });
    }
}
