<?php namespace Initbiz\CumulusCore\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Clusters extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    /**
     * @var array Permissions required to view this page.
     */
    public $requiredPermissions = ['initbiz.cumuluscore.access_clusters'];

    /**
     * @var string HTML body tag class
     */
    public $bodyClass = 'compact-container';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Initbiz.CumulusCore', 'cumulus-main-menu', 'cumulus-side-menu-clusters');
    }
}
