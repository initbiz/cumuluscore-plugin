<?php namespace Initbiz\CumulusCore\Controllers;

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

        BackendMenu::setContext('Initbiz.CumulusCore', 'cumulus-main-menu', 'cumulus-side-menu-users');
    }
}
