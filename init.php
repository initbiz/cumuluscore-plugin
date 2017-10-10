<?php namespace Initbiz\CumulusCore;

use Event;
use Initbiz\CumulusCore\Models\Cluster;
use RainLab\User\Controllers\Users as UserController;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Models\UserGroup;
use BackendMenu;
use Yaml;
use File;
use Lang;

UserModel::extend(function ($model) {
    $model->belongsToMany['clusters'] = [
        Cluster::class,
        'table' => 'initbiz_cumuluscore_cluster_user',
        'order' => 'full_name',
        'key'      => 'cluster_id',
        'otherKey' => 'user_id'
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

Event::listen('backend.menu.extendItems', function ($manager) {
    if ($manager->getContext()->owner === "RainLab.User"
        && $manager->getContext()->mainMenuCode === "user") {
        BackendMenu::setContext('Initbiz.CumulusCore', 'cumulus-main-menu', 'cumulus-side-menu-users');
    }
    $manager->removeMainMenuItem('RainLab.User', 'user');
});

Event::listen('rainlab.user.register', function ($user, $data) {
    // Add user to cluster automatically based on cluster variable from $data (need validation)
    /*
    $cluster = Cluster::where('slug', $data['cluster'])->first();
    if ($cluster) {
        $user->clusters()->add($cluster);
    }
    */

    // Uncomment following lines to automatically add a user to "registered" group
    /*
    $group = UserGroup::where('code', 'registered')->first();
    if ($group) {
        $user->groups()->add($group);
    }
    */
});
