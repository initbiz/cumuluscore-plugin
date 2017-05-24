<?php namespace InitBiz\Cumuluscore\Components;

use Cms\Classes\ComponentBase;
use InitBiz\CumulusCore\Classes\Helpers;

class MenuItem extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Menu item',
            'description' => 'Component that is going to be used on pages that we want to show in navigation'
        ];
    }

    public function defineProperties()
    {
        return [
            'menuItemTitle' => [
                'description' => 'User friendly title to be shown on button to this page', //TODO: from lang?
                'type' => 'string'
            ],
            'cumulusModule' => [
                'description' => 'Pick module to restrict user access',
                'type'        => 'dropdown'
            ]
        ];
    }

    public function getCumulusModuleOptions(){
        //TODO: get title from lang
        return ['none' => 'Bez uprawnień'] + Helpers::getModulesList();
    }

}
