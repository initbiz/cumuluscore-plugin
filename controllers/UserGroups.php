<?php

namespace Initbiz\CumulusCore\Controllers;

use BackendMenu;
use RainLab\User\Controllers\UserGroups as RainLabUserGroups;

class UserGroups extends RainLabUserGroups
{
    public function __construct()
    {
        parent::__construct();

        $this->addViewPath($this->guessViewPathFrom(RainLabUserGroups::class));

        $this->viewPath = array_reverse($this->viewPath);

        BackendMenu::setContext('Initbiz.CumulusCore', 'cumulus-main-menu', 'cumulus-side-menu-users');
    }
}
