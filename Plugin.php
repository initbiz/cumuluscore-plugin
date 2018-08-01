<?php namespace Initbiz\CumulusCore;

use System\Classes\PluginBase;
use Initbiz\CumulusCore\Classes\Helpers;

class Plugin extends PluginBase
{
    public $require = ['RainLab.User', 'RainLab.Notify', 'RainLab.Location', 'RainLab.UserPlus', 'RainLab.Pages'];

    public function registerComponents()
    {
        return [
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
                'permissions' => [],
                'order' => 100
            ],
            'modules' => [
                'label'       => 'initbiz.cumuluscore::lang.settings.menu_modules_label',
                'description'       => 'initbiz.cumuluscore::lang.settings.menu_modules_description',
                'category'       => 'initbiz.cumuluscore::lang.settings.menu_category',
                'icon' => 'icon-cubes',
                'url' => \Backend::url('initbiz/cumuluscore/modules'),
                'permissions' => [],
                'order' => 100
            ]
        ];
    }

    public function register()
    {
        $this->registerConsoleCommand('cumulus.createmodule', 'Initbiz\CumulusCore\Console\CreateModule');
    }
}
