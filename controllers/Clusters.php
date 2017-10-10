<?php namespace Initbiz\CumulusCore\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Clusters extends Controller
{
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';


    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Initbiz.CumulusCore', 'cumulus-main-menu', 'cumulus-side-menu-clusters');
    }
}
