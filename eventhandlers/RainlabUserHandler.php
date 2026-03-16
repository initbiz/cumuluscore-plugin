<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\EventHandlers;

use App;
use Backend\Classes\NavigationManager;
use Illuminate\Auth\Events\Logout;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\CumulusCore\Models\Cluster;
use Lang;
use RainLab\User\Components\Account;
use RainLab\User\Controllers\Users;
use RainLab\User\Models\User;
use Redirect;
use System;

class RainlabUserHandler
{
    public function subscribe($event)
    {
        $this->addClusterRelation($event);
        $this->addMethodsToUser($event);

        if (App::runningInFrontend() && System::hasModule('Cms')) {
            $this->addOnRedirectMeAjaxHandler($event);
            $this->forgetClusterOnLogout($event);
        }

        if (App::runningInBackend()) {
            $this->addFullNameColumn($event);
            $this->extendUsersController($event);
        }
    }

    public function addOnRedirectMeAjaxHandler($event)
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
                'key' => 'user_id',
                'otherKey' => 'cluster_id',
            ];
        });
    }

    public function addMethodsToUser($event)
    {
        User::extend(function ($model) {
            $model->addDynamicMethod('scopeActivated', function ($query) {
                return $query->where('is_activated', true);
            });

            $model->addDynamicMethod('scopeApplyTrashedFilter', function ($query, $type) {
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
                $model->loadMissing('clusters');

                return $model->clusters->firstWhere('slug', $cluster->slug) ? true : false;
            });

            $model->addDynamicMethod('getFullNameAttribute', function ($user) use ($model) {
                return $model->first_name.' '.$model->last_name;
            });

            $model->addDynamicMethod('getClusters', function () use ($model) {
                $model->loadMissing('clusters');

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
                        'label' => Lang::get('initbiz.cumuluscore::lang.users.last_first_name'),
                    ],
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

    protected function addPermissionsToUsersController($event): void
    {
        // Legacy support for initbiz.cumuluscore.access_users permission
        $event->listen('backend.menu.extendItems', function (NavigationManager $manager) {

            if ($sideItem = $manager->getSideMenuItem('RainLab.User', 'user', 'users')) {
                $config = $sideItem->getConfig();

                $config['permissions'][] = 'initbiz.cumuluscore.access_users';

                $manager->addSideMenuItem('RainLab.User', 'user', 'users', $config);
            }
        });

        Users::extend(function ($controller) {
            $controller->requiredPermissions = array_merge($controller->requiredPermissions, ['initbiz.cumuluscore.access_users']);
        });
    }

    public function extendUsersController($event)
    {
        $event->listen('rainlab.user.view.extendPreviewTabs', function () {
            return ['Clusters' => '$/initbiz/cumuluscore/partials/_user_clusters.php'];
        });

        Users::extendFormFields(function ($form, $model, $context) {
            if (! $model instanceof User) {
                return;
            }

            $form->addTabFields([
                'clusters' => [
                    'label' => 'Clusters',
                    'tab' => 'Clusters',
                    'type' => 'relation',
                    'controller' => [
                        'label' => 'Clusters',
                        'list' => '$/initbiz/cumuluscore/models/cluster/columns.yaml',
                        'fields' => '$/initbiz/cumuluscore/models/cluster/fields.yaml',
                    ],
                    'nameFrom' => 'name',
                    'select' => 'name',
                ],
            ]);
        });
    }
}
