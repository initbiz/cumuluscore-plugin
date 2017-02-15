<?php namespace InitBiz\Cumuluscore\Components;

use Cms\Classes\ComponentBase;

class MenuItem extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Cumulus MenuItem Component',
            'description' => 'Component to being used on pages to show in navigation'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

}
