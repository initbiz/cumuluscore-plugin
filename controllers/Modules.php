<?php namespace Initbiz\CumulusCore\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use System\Classes\SettingsManager;

/**
 * Modules Back-end Controller
 */
class Modules extends Controller
{
    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Initbiz.CumulusCore', 'modules');
    }
}
