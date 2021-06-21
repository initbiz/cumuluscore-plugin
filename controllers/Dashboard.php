<?php

namespace Initbiz\CumulusCore\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Cumulus dashboard
 */
class Dashboard extends Controller
{
    /**
     * @var array Permissions required to view this page.
     */
    public $requiredPermissions = ['initbiz.cumuluscore.access*'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Initbiz.CumulusCore', 'cumulus-main-menu');
    }

    public function index()
    {
        $this->vars['user'] = $this->user;
    }
}
