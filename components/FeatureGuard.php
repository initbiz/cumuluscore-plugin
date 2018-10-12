<?php namespace Initbiz\CumulusCore\Components;

use Cms\Classes\ComponentBase;
use Initbiz\CumulusCore\Classes\FeatureManager;
use Initbiz\CumulusCore\Repositories\ClusterRepository;

class FeatureGuard extends ComponentBase
{
    use \Initbiz\CumulusCore\Traits\CumulusComponentProperties;

    public $clusterRepository;

    protected $featureManager;

    public function componentDetails()
    {
        return [
            'name' => 'initbiz.cumuluscore::lang.feature_guard.name',
            'description' => 'initbiz.cumuluscore::lang.feature_guard.description'
        ];
    }

    public function defineProperties()
    {
        return $this->defineClusterSlug() +
        [
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
        $this->clusterRepository = new ClusterRepository;

        $clusterSlug = $this->property('clusterSlug');

        $featuresOnPage = $this->property('cumulusFeatures');

        $canEnter = false;

        foreach ($featuresOnPage as $feature) {
            if ($this->clusterRepository->canEnterFeature($clusterSlug, $feature)) {
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
        $this->featureManager = FeatureManager::instance();
        return $this->featureManager->getFeaturesOptions();
    }
}
