<?php namespace Initbiz\CumulusCore;

use Db;
use Yaml;
use File;
use Lang;
use Event;
use Cookie;
use Session;
use Redirect;
use Controller;
use BackendMenu;
use RainLab\User\Models\UserGroup;
use RainLab\User\Components\Account;
use Initbiz\CumulusCore\Models\Cluster;
use RainLab\User\Models\User as UserModel;
use Initbiz\CumulusCore\Classes\MenuManager;
use Initbiz\CumulusCore\Classes\FeatureManager;
use Initbiz\CumulusCore\Models\AutoAssignSettings;
use RainLab\User\Controllers\Users as UserController;
use Initbiz\CumulusCore\Repositories\ClusterRepository;

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

    $clusterRepository = new ClusterRepository();

    if (AutoAssignSettings::get('auto_assign_user') === 'concrete_cluster') {
        try {
            $clusterRepository->addUserToCluster($user->id, AutoAssignSettings::get('auto_assign_user_concrete_cluster'));
        } catch (\Exception $e) {
            Db::rollback();
            throw new \Exception("Error Assigning user to concrete cluster", 1);
        }
    }

    if (AutoAssignSettings::get('auto_assign_user') === 'get_cluster') {
        $clusterSlug = $data[AutoAssignSettings::get('auto_assign_user_get_cluster')];

        try {
            $clusterRepository->addUserToCluster($user->id, $clusterSlug);
        } catch (\Exception $e) {
            Db::rollback();
            throw new \Exception("Error Assigning user to existing cluster with slug get from variable", 1);
        }
    }
    if (AutoAssignSettings::get('auto_assign_user') === 'new_cluster') {
        Event::fire('initbiz.cumuluscore.beforeAutoAssignNewCluster', [&$data]);

        $createClusterData = [
            'name' => $data[AutoAssignSettings::get('auto_assign_user_new_cluster')],
            'thoroughfare'   => (isset($data['thoroughfare']))? $data['thoroughfare']: null,
            'city'           => (isset($data['city']))? $data['city']: null,
            'phone'          => (isset($data['phone']))? $data['phone']: null,
            'country_id'     => (isset($data['country_id']))? $data['country_id']: null,
            'postal_code'    => (isset($data['postal_code']))? $data['postal_code']: null,
            'description'    => (isset($data['description']))? $data['description']: null,
            'email'          => (isset($data['cluster_email']))? $data['cluster_email']: null,
            'tax_number'     => (isset($data['tax_number']))? $data['tax_number']: null,
            'account_number' => (isset($data['account_number']))? $data['account_number']: null,
        ];

        $cluster = $clusterRepository->create($createClusterData);

        try {
            $clusterRepository->addUserToCluster($user->id, $cluster->slug);
        } catch (\Exception $e) {
            Db::rollback();
            throw new \Exception("Error Assigning user to new cluster", 1);
        }

        //TODO move this to other methods, add some try catches
        if (AutoAssignSettings::get('enable_auto_assign_cluster')) {
            $planSlug = "";

            if (AutoAssignSettings::get('auto_assign_cluster') === 'get_plan') {
                $planSlug = $data[AutoAssignSettings::get('auto_assign_cluster_get_plan')];
            }

            if (AutoAssignSettings::get('auto_assign_cluster') === 'concrete_plan') {
                $planSlug = AutoAssignSettings::get('auto_assign_cluster_concrete_plan');
            }

            try {
                $clusterRepository->addClusterToPlan($cluster->slug, $planSlug);
            } catch (\Exception $e) {
                Db::rollback();
                throw new \Exception("Error assigning cluster to plan", 1);
            }
        }
    }
}, 100);


Event::listen('rainlab.user.register', function ($user, $data) {
    if (!AutoAssignSettings::get('enable_auto_assign_user_to_group')) {
        return true;
    }

    //TODO: move to repository, but what to do with those UserModel and UserController at the top of this file?
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

    $widget->addTabFields([
        'viewBag[cumulusFeatures]' => [
            'tab' => 'initbiz.cumuluscore::lang.menu_item.cumulus_tab_label',
            'label' => 'initbiz.cumuluscore::lang.menu_item.cumulus_features',
            'comment' => 'initbiz.cumuluscore::lang.menu_item.cumulus_features_comment',
            'type' => 'checkboxlist',
            'options' => $features,
        ]
    ]);
});
