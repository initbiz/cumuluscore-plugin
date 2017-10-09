<?php namespace Initbiz\Cumuluscore\Components;

use Cms\Classes\ComponentBase;
use InitBiz\CumulusCore\Classes\Helpers;
use InitBiz\CumulusCore\Models\Company;
use InitBiz\CumulusCore\Models\Module;

class ModuleGuard extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Module Guard',
            'description' => 'Guard component that allows company enter the module'
        ];
    }

    public function defineProperties()
    {
        return [
            'companySlug' => [
                'title'       => 'Company slug',
                'description' => 'Slug of company that dashboard is going to be shown',
                'type' => 'string',
                'default' => '{{ :company }}'
            ],
            'cumulusModule' => [
                'description' => 'Pick module to restrict user access',
                'type'        => 'dropdown'
            ]
        ];
    }

    public function onRun()
    {
        if (!$this->canEnterModule($this->property('companySlug'), $this->property('cumulusModule'))) {
            $this->setStatusCode(403);
            return $this->controller->run('403');
        }
    }

    public function getCumulusModuleOptions(){
        //TODO: get title from lang
        return Helpers::getModulesList();
    }

    public function canEnterModule($companySlug, $moduleSlug)
    {
        return Company::whereSlug($companySlug)->first()->plan()->first()->modules()->whereSlug($moduleSlug)->first() ? true : false;
    }
}
