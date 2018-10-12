<?php namespace Initbiz\CumulusCore\Components;

use Lang;
use Cms\Classes\ComponentBase;
use Initbiz\CumulusCore\Classes\FeatureManager;

class MenuItem extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'initbiz.cumuluscore::lang.menu_item.name',
            'description' => 'initbiz.cumuluscore::lang.menu_item.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'menuItemTitle' => [
                'title' => 'initbiz.cumuluscore::lang.menu_item.menu_item_title',
                'description' => 'initbiz.cumuluscore::lang.menu_item.menu_item_title_desc',
                'type' => 'string'
            ],
            'cumulusFeatures' => [
                'title' => 'initbiz.cumuluscore::lang.menu_item.cumulus_features',
                'description' => 'initbiz.cumuluscore::lang.menu_item.cumulus_features_desc',
                'placeholder' => '*',
                'type'        => 'set',
                'default'     => []
            ]
        ];
    }

    public function getCumulusFeaturesOptions()
    {
        $featureManager = FeatureManager::instance();
        return ['none' => Lang::get('initbiz.cumuluscore::lang.menu_item.cumulus_feature_none')] + $featureManager->getFeaturesOptions();
    }
}
