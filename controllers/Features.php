<?php

namespace Initbiz\CumulusCore\Controllers;

use Lang;
use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;
use Initbiz\CumulusCore\Classes\FeatureManager;

/**
 * Features Back-end Controller
 */
class Features extends Controller
{
    protected $featureManager;

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Initbiz.CumulusCore', 'features');

        $this->featureManager = FeatureManager::instance();
    }

    public function index()
    {
        $this->bodyClass = 'compact-container';
        $this->pageTitle = 'initbiz.cumuluscore::lang.settings.features_page_title';
        $this->vars['features'] = $this->featureManager->getFeatures();
    }

    public function onClearCache()
    {
        $this->featureManager->clearCache();

        Flash::success(Lang::get('rainlab.translate::lang.messages.clear_cache_success'));

        $features = $this->featureManager->getFeatures();

        return ['#features-table' => $this->makePartial('features_table', ['features' => $features])];
    }
}
