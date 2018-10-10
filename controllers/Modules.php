<?php namespace Initbiz\CumulusCore\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Initbiz\CumulusCore\Classes\FeatureManager;
use System\Classes\SettingsManager;

/**
 * Modules Back-end Controller
 */
class Modules extends Controller
{
    public $implement = [
        'Backend.Behaviors.ListController'
    ];

    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Initbiz.CumulusCore', 'modules');
    }

    public function onScanPurgeFeatures()
    {
        $featureManager = FeatureManager::instance();
        $featureManager->getFeatures();
    }
}
