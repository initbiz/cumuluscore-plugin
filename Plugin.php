<?php namespace Initbiz\CumulusCore;

// use Initbiz\CumulusCore\Classes\Connector as CumulusConnector;
use Initbiz\CumulusCore\Classes\Helpers;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = ['RainLab.UserPlus'];

    public function registerComponents()
    {
        return [
            'Initbiz\CumulusCore\Components\CumulusGuard'       =>  'cumulusGuard',
            'Initbiz\CumulusCore\Components\ModuleGuard'       =>  'moduleGuard',
            'Initbiz\CumulusCore\Components\UserClustersList'  =>  'clustersList',
            'Initbiz\CumulusCore\Components\Menu'               =>  'menu',
            'Initbiz\CumulusCore\Components\MenuItem'               =>  'menuItem',
            'Initbiz\CumulusCore\Components\ClusterDashboard'   =>  'clusterDashboard'
        ];
    }

    public function registerSettings()
    {
        return [
            'modules' => [
                'label'       => 'initbiz.cumuluscore::lang.settings.menu_label',
                'description'       => 'initbiz.cumuluscore::lang.settings.menu_description',
                'category'       => 'initbiz.cumuluscore::lang.settings.menu_category',
                'icon' => 'icon-cubes',
                'url' => \Backend::url('initbiz/cumuluscore/modules'),
                'permissions' => [],
                'order' => 100
            ]
        ];
    }

    public function boot()
    {
    }

    public function register()
    {
        $this->registerConsoleCommand('cumulus.createmodule', 'Initbiz\CumulusCore\Console\CreateModule');
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'componentPage' => [$this, 'findComponentPageUrl']
            ]
        ];
    }

    public function findComponentPageUrl($text)
    {
        return Helpers::findComponentPage($text)->url;
    }
}
