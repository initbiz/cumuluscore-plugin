<?php namespace Initbiz\CumulusCore\Classes;

use Lang;
use Cache;
use Event;
use System\Classes\PluginManager;
use October\Rain\Support\Singleton;

class FeatureManager extends Singleton
{
    /**
     * @var \System\Classes\PluginManager
     */
    protected $pluginManager;

    /**
     * Initialize this singleton.
     */
    protected function init()
    {
        $this->pluginManager = PluginManager::instance();
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
        $plugins = $this->pluginManager->getPlugins();

        $pluginsNamespaces = $this->pluginManager->getPluginNamespaces();

        $cumulusFeatures = [];

        foreach ($plugins as $plugin) {
            if (method_exists($plugin, 'registerCumulusFeatures')) {
                if (!is_array($features = $plugin->registerCumulusFeatures())) {
                    continue;
                }
                $cumulusFeatures = array_merge($cumulusFeatures, $features);
            }
        }

        return $cumulusFeatures;
    }

    public function clearCache()
    {
        Cache::forget('cumulusFeatures');
        $features = $this->getFeatures();
        Cache::forever('cumulusFeatures', $features);
    }
}
