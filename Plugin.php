<?php namespace Initbiz\CumulusCore;

use System\Classes\PluginBase;
use Initbiz\CumulusCore\Classes\Helpers;

class Plugin extends PluginBase
{
    public $require = ['RainLab.User', 'RainLab.Notify', 'RainLab.Location', 'RainLab.UserPlus'];

    public function registerComponents()
    {
        return [
            'Initbiz\CumulusCore\Components\Menu'              =>  'menu',
            'Initbiz\CumulusCore\Components\MenuItem'          =>  'menuItem',
            'Initbiz\CumulusCore\Components\ModuleGuard'       =>  'moduleGuard',
            'Initbiz\CumulusCore\Components\CumulusGuard'      =>  'cumulusGuard',
            'Initbiz\CumulusCore\Components\UserClustersList'  =>  'clustersList',
        ];
    }

    public function registerSettings()
    {
        return [
            'auto_assign' => [
                'label'       => 'initbiz.cumuluscore::lang.settings.menu_auto_assign_label',
                'description'       => 'initbiz.cumuluscore::lang.settings.menu_auto_assign_description',
                'category'       => 'initbiz.cumuluscore::lang.settings.menu_category',
                'icon' => 'icon-sitemap',
                'class' => 'Initbiz\CumulusCore\Models\Settings',
                'permissions' => ['initbiz.cumuluscore.settings_access_auto_assign'],
                'order' => 100
            ],
            'modules' => [
                'label'       => 'initbiz.cumuluscore::lang.settings.menu_modules_label',
                'description'       => 'initbiz.cumuluscore::lang.settings.menu_modules_description',
                'category'       => 'initbiz.cumuluscore::lang.settings.menu_category',
                'icon' => 'icon-cubes',
                'url' => \Backend::url('initbiz/cumuluscore/modules'),
                'permissions' => ['initbiz.cumuluscore.settings_access_manage_modules'],
                'order' => 100
            ]
        ];
    }

    public function register()
    {
        $this->registerConsoleCommand('cumulus.createmodule', 'Initbiz\CumulusCore\Console\CreateModule');
    }
}
