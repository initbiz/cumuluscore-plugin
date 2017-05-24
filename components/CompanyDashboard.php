<?php namespace InitBiz\Cumuluscore\Components;

use Cms\Classes\ComponentBase;
use RainLab\User\Models\User as Users;

class CompanyDashboard extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Company Dashboard',
            'description' => 'Company dashboard component'
        ];
    }

    public function onRun()
    {
        $this->page['workersNumber'] = Users::whereHas('companies', function ($query) {
            $query->where('slug', $this->property('companySlug'));
        })->count();
    }

    public function defineProperties()
    {
        return [
            'companySlug' => [
                'title'       => 'Company slug',
                'description' => 'Slug of company that dashboard is going to be shown',
                'type' => 'string',
                'default' => '{{ :company }}'
            ]
        ];
    }

}
