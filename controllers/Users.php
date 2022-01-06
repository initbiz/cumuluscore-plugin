<?php

namespace Initbiz\CumulusCore\Controllers;

use BackendMenu;
use RainLab\User\Controllers\Users as RainLabUsers;

class Users extends RainLabUsers
{
    /**
     * @var array Extensions implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\RelationController::class,
        \Backend\Behaviors\ListController::class
    ];

    public $relationConfig = 'config_relation.yaml';

    /**
     * @var array Permissions required to view this page.
     */
    public $requiredPermissions = ['initbiz.cumuluscore.access_users'];

    public function __construct()
    {
        parent::__construct();

        $this->addViewPath($this->guessViewPathFrom(RainLabUsers::class));

        $this->viewPath = array_reverse($this->viewPath);

        BackendMenu::setContext('Initbiz.CumulusCore', 'cumulus-main-menu', 'cumulus-side-menu-users');
    }

    public function formExtendFields($form)
    {
        $config = [];
        $config['clusters'] = [
            'tab'       => 'initbiz.cumuluscore::lang.users.cluster_tab',
            'type'      => 'partial',
            'path'      => 'plugins/initbiz/cumuluscore/controllers/users/_clusters.htm',
        ];

        $form->addTabFields($config);
    }
}
