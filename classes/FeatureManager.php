<?php namespace Initbiz\CumulusCore\Classes;

use Lang;
use Cache;
use Event;
use October\Rain\Support\Singleton;

class FeatureManager extends Singleton
{
    /**
     * @var PluginRegistrationManager
     */
    protected $pluginRegistrationManager;

    /**
     * Initialize this singleton.
     */
    protected function init()
    {
        $this->pluginRegistrationManager = PluginRegistrationManager::instance();
    }

    /**
     * Get features
     * @return array cumulus features array
     */
    public function getFeatures()
    {
        if (Cache::has('cumulusFeatures')) {
            $features = Cache::get('cumulusFeatures');
            return $features;
        }

        $features = $this->scanFeatures();

        Cache::forever('cumulusFeatures', $features);

        return $features;
    }

    public function getFeaturesOptions()
    {
        $features = $this->getFeatures();
        $featureOptions = [];
        foreach ($features as $featureCode => $featureDef) {
            $name = $featureDef['name'] ?? $featureCode;
            $description = $featureDef['description'] ?? "";

            $featureOptions[$featureCode] = [
                Lang::get($name),
                Lang::get($description)
            ];
        }
        return $featureOptions;
    }

    public function scanFeatures()
    {
        $cumulusFeatures = $this->pluginRegistrationManager->runMethod('registerCumulusFeatures');

        return $cumulusFeatures;
    }

    public function clearCache()
    {
        Cache::forget('cumulusFeatures');
        $features = $this->getFeatures();
    }
}
