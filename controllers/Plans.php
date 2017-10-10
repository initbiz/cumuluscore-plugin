<?php namespace Initbiz\CumulusCore\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Plans extends Controller
{
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Initbiz.CumulusCore', 'cumulus-main-menu', 'cumulus-side-menu-plan');
    }
}
