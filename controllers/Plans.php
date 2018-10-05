<?php namespace Initbiz\CumulusCore\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Plans extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\ReorderController'
    ];

    /**
     * @var array Permissions required to view this page.
     */
    public $requiredPermissions = ['initbiz.cumuluscore.access_plans'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    
    /**
     * @var string HTML body tag class
     */
    public $bodyClass = 'compact-container';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Initbiz.CumulusCore', 'cumulus-main-menu', 'cumulus-side-menu-plan');
    }
}
