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

    public function getFeatures()
    {
        $plugins = $this->pluginManager->getPlugins();

        $pluginsNamespaces = $this->pluginManager->getPluginNamespaces();

        $cumulusFeatures = [];

        foreach ($plugins as $plugin) {
            if (method_exists($plugin, 'registerCumulusFeatures')) {
                if (!is_array($features = $plugin->registerCumulusFeatures())) {
                    continue;
                }
                //get nice plugin code slug, like Initbiz.CumulusCore
                $class = get_class($plugin);
                $pluginCode = explode('\\', $class);
                array_pop($pluginCode);
                $pluginCode = implode('.', $pluginCode);
                $cumulusFeatures[$pluginCode] = $features;
            }
        }

        return $cumulusFeatures;
    }

    public function scanFeatures()
    {
    }
}
