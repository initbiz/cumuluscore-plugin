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
    /**
     * Initialize this singleton.
     */
    protected function init()
    {
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

    /**
     * Get features in list syntax:
     * [
     *    'code' => [
     *        'name' => 'name/code',
     *        'description' => 'description'
     *    ]
     * ]
     *  
     * @return array
     */
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

    /**
     * Get features in inspector options syntax:
     * [
     *     'code' => 'name/code'
     * ]
     *
     * @return array
     */
    public function getFeaturesOptionsInspector()
    {
        $features = $this->getFeatures();

        $featureOptions = [];

        foreach ($features as $featureCode => $featureDef) {
            $featureOptions[$featureCode] = $featureDef['name'] ?? $featureCode;
        }

        return $featureOptions;
    }

    /**
     * Runs registerCumulusFeatures and returns the combination of arrays
     *
     * @return array
     */
    public function scanFeatures()
    {
        return PluginRegistrationManager::instance()->runMethod('registerCumulusFeatures');
    }

    public function clearCache()
    {
        Cache::forget(self::CACHEKEY);
        $features = $this->getFeatures();
    }
}
