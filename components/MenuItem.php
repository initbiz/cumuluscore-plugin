<?php namespace Initbiz\CumulusCore\Components;

use Lang;
use Cms\Classes\ComponentBase;
use Initbiz\CumulusCore\Classes\Helpers;

class MenuItem extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'initbiz.cumuluscore::lang.menu_item.name',
            'description' => 'initbiz.cumuluscore::lang.menu_item.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'menuItemTitle' => [
                'title' => 'initbiz.cumuluscore::lang.menu_item.menu_item_title',
                'description' => 'initbiz.cumuluscore::lang.menu_item.menu_item_title_desc',
                'type' => 'string'
            ],
            'cumulusModule' => [
                'title' => 'initbiz.cumuluscore::lang.menu_item.cumulus_module',
                'description' => 'initbiz.cumuluscore::lang.menu_item.cumulus_module_desc',
                'type'        => 'dropdown'
            ]
        ];
    }

    public function getCumulusModuleOptions()
    {
        //TODO: get title from lang
        return ['none' => Lang::get('initbiz.cumuluscore::lang.menu_item.cumulus_module_none')] + Helpers::getModulesList();
    }
}
