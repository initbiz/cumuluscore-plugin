<?php namespace Initbiz\CumulusCore\Classes;

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
     * Get features without looking for them in cache
     * @return array cumulus features array
     */
    public function getCleanFeatures()
    {
        $plugins = $this->pluginManager->getPlugins();

        $pluginsNamespaces = $this->pluginManager->getPluginNamespaces();

        $cumulusFeatures = [];

        foreach ($plugins as $plugin) {
            if (method_exists($plugin, 'registerCumulusFeatures')) {
                if (!is_array($features = $plugin->registerCumulusFeatures())) {
                    continue;
                }
                $cumulusFeatures[] = $features;
            }
        }

        //TODO: add cache support
        return $cumulusFeatures;
    }

    public function refreshFeatures()
    {
        //TODO: clear cache and
    }

    public function getFeatures()
    {
        //TODO: If in cache, return, if not get fresh
    }
}
