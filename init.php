<?php namespace Initbiz\CumulusCore;

use Db;
use File;
use Lang;
use Yaml;
use Event;
use Cookie;
use Session;
use Redirect;
use Controller;
use BackendMenu;
use RainLab\User\Models\UserGroup;
use Initbiz\CumulusCore\Models\Plan;
use RainLab\User\Components\Account;
use Initbiz\CumulusCore\Models\Cluster;
use RainLab\User\Models\User as UserModel;
use Initbiz\CumulusCore\Classes\MenuManager;
use Initbiz\CumulusCore\Classes\FeatureManager;
use Initbiz\CumulusCore\Models\AutoAssignSettings;
use RainLab\User\Controllers\Users as UserController;

Account::extend(function ($component) {
    $component->addDynamicMethod('onRedirectMe', function () use ($component) {
        return Redirect::to($component->pageUrl($component->property('redirect')));
    });
});

UserModel::extend(function ($model) {
    $model->belongsToMany['clusters'] = [
        Cluster::class,
        'table' => 'initbiz_cumuluscore_cluster_user',
        'order' => 'name',
        'key'      => 'user_id',
        'otherKey' => 'cluster_id'
    ];

    $model->addDynamicMethod('scopeActivated', function ($query) use ($model) {
        return $query->where("is_activated", true);
    });

    $model->addDynamicMethod('canEnter', function ($cluster) use ($model) {
        return $model->clusters()->whereSlug($cluster->slug)->first() ? true : false;
    });
});

UserController::extendFormFields(function ($widget) {
    // Prevent extending of related form instead of the intended User form
    if (!$widget->model instanceof UserModel) {
        return;
    }

    $configFile = __DIR__ . '/config/clusters_field.yaml';
    $config = Yaml::parse(File::get($configFile));
    $widget->addTabFields($config);
});

Event::listen('backend.list.extendColumns', function ($widget) {
    if ($widget->getController() instanceof UserController) {
        $widget->removeColumn('name');
        $widget->addColumns(['full_name' => [
            'label' => Lang::get('initbiz.cumuluscore::lang.users.last_first_name'),
            'select' => 'concat(surname, \' \', name)'
        ]
        ]);
    }
});

Event::listen('rainlab.user.register', function ($user, $data) {
    if (!AutoAssignSettings::get('enable_auto_assign_user')) {
        return true;
    }

    Event::fire('initbiz.cumuluscore.beforeAutoAssignUserToCluster', [$data]);

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
        Event::fire('initbiz.cumuluscore.beforeAutoAssignClusterToPlan', [$data]);

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


Event::listen('rainlab.user.register', function ($user, $data) {
    if (!AutoAssignSettings::get('enable_auto_assign_user_to_group')) {
        return true;
    }

    $group = UserGroup::where('code', AutoAssignSettings::get('group_to_auto_assign_user'))->first();
    if ($group) {
        $user->groups()->add($group);
    }
}, 90);

// start transaction before register user
Event::listen('rainlab.user.beforeRegister', function (&$data) {
    Db::beginTransaction();
}, 500);

Event::listen('rainlab.user.register', function ($user, $data) {
    //TODO: ensure this one makes sense, when creating cluster fails the user is created
    Db::commit();
}, 10);

Event::listen('rainlab.user.logout', function ($user) {
    Session::pull('cumulus_clusterslug');
    Cookie::queue(Cookie::forget('cumulus_clusterslug'));
}, 100);


// Register menu items for the RainLab.Pages plugin
Event::listen('pages.menuitem.listTypes', function () {
    return [
        'cumulus-page' => 'initbiz.cumuluscore::lang.menu_item.cumulus_page',
    ];
});

Event::listen('pages.menuitem.getTypeInfo', function ($type) {
    $result = null;

    if ($type == 'cumulus-page') {
        $menuManager = MenuManager::instance();
        $result = $menuManager->getCmsPages();
    }

    return $result;
});

Event::listen('pages.menuitem.resolveItem', function ($type, $item, $url, $theme) {
    $result = null;

    if ($item->type === 'cumulus-page') {
        $menuManager = MenuManager::instance();
        $result = $menuManager->resolveItem($item, $url, $theme);
    }

    return $result;
});

Event::listen('pages.menu.referencesGenerated', function (&$items) {
    $menuManager = MenuManager::instance();
    $items = $menuManager->hideClusterMenuItems($items);
});

Event::listen('backend.form.extendFields', function ($widget) {
    if (
        !$widget->getController() instanceof \RainLab\Pages\Controllers\Index ||
        !$widget->model instanceof \RainLab\Pages\Classes\MenuItem
    ) {
        return;
    }

    $featureManager = FeatureManager::instance();
    $features = $featureManager->getFeaturesOptions();

    //TODO: this should be added as checkboxlist or taglist, the problem is that
    //      the rainlab/pages/formwidgets/assets/js/menu-items-editor.js
    //      gets and sets values using jquery not PHP to get value
    //      as a consequence it sets only last value to yaml
    //      and cannot set value of select2 (taglist) or checkboxlist
    $featureFields = [];

    foreach ($features as $featureCode => $featureDef) {
        $featureFields['viewBag[cumulusFeature-'.$featureCode.']'] = [
            'tab' => 'initbiz.cumuluscore::lang.menu_item.cumulus_tab_label',
            'label' => $featureDef[0],
            'commentAbove' => $featureDef[1],
            'type' => 'checkbox',
        ];
    }
    $widget->addTabFields($featureFields);
});
