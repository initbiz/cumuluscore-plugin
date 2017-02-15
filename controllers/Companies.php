<?php namespace InitBiz\CumulusCore\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Companies extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';


    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('InitBiz.CumulusCore', 'cumulus-main-menu', 'cumulus-side-menu-companies');
    }
}
