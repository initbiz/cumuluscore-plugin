<?php

namespace Initbiz\CumulusCore\Components;

use Cms\Classes\ComponentBase;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\CumulusCore\Classes\FeatureManager;

class FeatureGuard extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'initbiz.cumuluscore::lang.feature_guard.name',
            'description' => 'initbiz.cumuluscore::lang.feature_guard.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'clusterUniq' => [
                'title' => 'initbiz.cumuluscore::lang.component_properties.cluster_uniq',
                'description' => 'initbiz.cumuluscore::lang.component_properties.cluster_uniq_desc',
                'type' => 'string',
                'default' => '{{ :cluster }}'
            ],
            'cumulusFeatures' => [
                'title' => 'initbiz.cumuluscore::lang.feature_guard.cumulus_features',
                'description' => 'initbiz.cumuluscore::lang.feature_guard.cumulus_features_desc',
                'placeholder' => '*',
                'type'        => 'set',
                'default'     => []
            ]
        ];
    }

    public function onRun()
    {
        $cluster = Helpers::getClusterFromUrlParam($this->property('clusterUniq'));

        $featureCodes = $this->property('cumulusFeatures');

        $canEnter = false;

        foreach ($featureCodes as $featureCode) {
            if ($cluster->canEnterFeature($featureCode)) {
                $canEnter = true;
                break;
            }
        }

        if (!$canEnter) {
            $this->setStatusCode(403);
            return $this->controller->run('403');
        }
    }

    public function getCumulusFeaturesOptions()
    {
        return FeatureManager::instance()->getFeaturesOptionsInspector();
    }
}
