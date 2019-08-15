<?php namespace Initbiz\CumulusCore\Classes;

use Lang;
use Cache;
use Event;
use October\Rain\Support\Singleton;
use Initbiz\InitDry\Classes\PluginRegistrationManager;

class FeatureManager extends Singleton
{
    /**
     * Key in cache to store layouts
     * @var string
     */
    public const CACHEKEY = 'cumulusFeatures';

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
        if (env('APP_DEBUG', false)) {
            Cache::forget(self::CACHEKEY); // clear cache for development purposes
        }

        if (Cache::has('cumulusFeatures')) {
            $features = Cache::get(self::CACHEKEY);
            return $features;
        }

        $features = $this->scanFeatures();

        Cache::forever(self::CACHEKEY, $features);

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
        Cache::forget(self::CACHEKEY);
        $features = $this->getFeatures();
    }
}
