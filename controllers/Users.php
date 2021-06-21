<?php

namespace Initbiz\CumulusCore\Controllers;

use BackendMenu;
use RainLab\User\Controllers\Users as RainLabUsers;

class Users extends RainLabUsers
{
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
}
