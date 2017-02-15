<?php namespace InitBiz\Cumuluscore\Components;

use Cms\Classes\ComponentBase;
use Event;
use InitBiz\CumulusCore\Classes\Helpers;
use InitBiz\CumulusCore\Models\Company;

class Menu extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Menu Component',
            'description' => 'Component that gives all menu entries'
        ];
    }

    public function onRun()
    {
        //Building navigation

        $moduleComponents = [];
        Event::fire('initbiz.cumuluscore.menuItems', [&$moduleComponents]);

        //TODO we are currently working on better solution
        $current_company_modules = Company::with('modules')
            ->where('slug', $this->param('company'))
            ->first()
            ->modules()
            ->get()
            ->pluck('name')
            ->values()
            ->map(function ($item, $key) {
                return str_slug($item);
            })
            ->toArray();

        $cmsPages = [];
        foreach ($moduleComponents as $component) {
            if ($component['componentModule'] === null
                || in_array($component['componentModule'], $current_company_modules, true)
            ) {
                $cmsPages[$component['componentTitle']] =
                    Helpers::findComponentPage($component['componentAlias'])['fileName'];
            }
        }
        $this->page['componentsWithUrls'] = $cmsPages;
    }

    public function defineProperties()
    {
        return [];
    }

}
