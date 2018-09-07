<?php namespace Initbiz\CumulusCore\Controllers;

use BackendMenu;
use RainLab\User\Controllers\Users as RainLabUsers;

class Users extends RainLabUsers
{
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Initbiz.CumulusCore', 'cumulus-main-menu', 'cumulus-side-menu-users');
    }
}
