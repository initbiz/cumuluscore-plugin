<?php namespace InitBiz\CumulusCore;

use Event;
use InitBiz\CumulusCore\Models\Company;
use RainLab\User\Controllers\Users as UserController;
use RainLab\User\Models\User as UserModel;
use BackendMenu;
use Yaml;
use File;

UserModel::extend(function ($model) {
    $model->belongsToMany['companies'] = [
        Company::class,
        'table' => 'initbiz_cumuluscore_company_user',
        'order' => 'full_name'
    ];
});

UserController::extendFormFields(function ($widget) {
    // Prevent extending of related form instead of the intended User form
    if (!$widget->model instanceof UserModel) {
        return;
    }

    $configFile = __DIR__ . '/config/companies_field.yaml';
    $config = Yaml::parse(File::get($configFile));
    $widget->addTabFields($config);
});

Event::listen('backend.list.extendColumns', function ($widget) {

    if ($widget->getController() instanceof UserController) {
        $widget->removeColumn('name');
        $widget->addColumns(['full_name' => [
            'label' => 'Nazwisko i imię',
            'select' => 'concat(surname, \' \', name)'
        ]
        ]);
    }
});

Event::listen('backend.menu.extendItems', function($manager)
{
    if($manager->getContext()->owner === "RainLab.User"
        && $manager->getContext()->mainMenuCode === "user") {
        BackendMenu::setContext('InitBiz.CumulusCore', 'cumulus-main-menu', 'cumulus-side-menu-users');
    }
    $manager->removeMainMenuItem('RainLab.User', 'user');
});


