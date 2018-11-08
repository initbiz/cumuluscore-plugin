<?php namespace Initbiz\CumulusCore\Classes;

use System\Classes\PluginManager;
use October\Rain\Support\Singleton;

/**
 * Class to get plugins' registrations
 */
class PluginRegistrationManager extends Singleton
{
    /**
     * @var PluginManager
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
     * Runs methods from plugins and merge their results to one array
     * @param  string $methodName method name to run in plugins.php
     * @return array              result of running $methodName methods
     */
    public function runMethod(string $methodName)
    {
        $plugins = $this->pluginManager->getPlugins();

        $result = [];

        foreach ($plugins as $plugin) {
            if (method_exists($plugin, $methodName)) {
                $methodResult = $plugin->$methodName();
                $result = array_merge($result, $methodResult);
            }
        }

        return $result;
    }
}
