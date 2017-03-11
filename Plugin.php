<?php namespace InitBiz\CumulusCore;

use InitBiz\CumulusCore\Classes\Connector as CumulusConnector;
use InitBiz\CumulusCore\Classes\Helpers;
use System\Classes\PluginBase;


class Plugin extends PluginBase
{
    public $require = ['RainLab.UserPlus'];

    private $menu = [
        'companiesList' => 'Choose company page',   //TODO: take it from lang
        'companyDashboard' => 'Company dashboard page'   //TODO: take it from lang
    ];

    public function registerComponents()
    {
        return [
            'InitBiz\CumulusCore\Components\CumulusGuard'       =>  'cumulusGuard',
            'InitBiz\CumulusCore\Components\UserCompaniesList'  =>  'companiesList',
            'InitBiz\CumulusCore\Components\Menu'               =>  'menu',
            'InitBiz\CumulusCore\Components\CompanyDashboard'   =>  'companyDashboard'
        ];
    }

    public function registerSettings()
    {
        return [
            'modules' => [
                'label' => 'Modules',
                'description' => '',
                'category' => 'Cumulus',
                'icon' => 'icon-cubes',
                'url' => \Backend::url('initbiz/cumuluscore/modules'),
                'permissions' => [],
                'order' => 100
            ]
        ];
    }

    public function boot()
    {
        CumulusConnector::registerNavigation($this->menu);
    }

    public function register()
    {
        $this->registerConsoleCommand('cumulus.createmodule', 'InitBiz\CumulusCore\Console\CreateModule');
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
