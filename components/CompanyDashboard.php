<?php namespace InitBiz\Cumuluscore\Components;

use Cms\Classes\ComponentBase;
use RainLab\User\Models\User as Users;

class CompanyDashboard extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'CompanyDashboard Component',
            'description' => 'Component that should be used on dashboard page'
        ];
    }

    public function onRun()
    {
        $this->page['workersNumber'] = Users::whereHas('companies', function ($query) {
            $query->where('slug', $this->param('company'));
        })->count();
    }

    public function defineProperties()
    {
        return [];
    }

}
