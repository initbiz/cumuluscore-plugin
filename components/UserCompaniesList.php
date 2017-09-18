<?php namespace InitBiz\CumulusCore\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;

class UserCompaniesList extends ComponentBase
{

    public $companyPage;
    public $companiesList;

    public $implement = [
        'InitBiz.CumulusCore.Behaviors.ComponentBehavior'
    ];

    public function componentDetails()
    {
        return [
            'name' => 'Companies list',
            'description' => 'Component showing all companies that user is assigned to'
        ];
    }

    public function defineProperties()
    {
        return [
            'companyPage' => [
                'title' => 'Company dashboard page',
                'description' => 'Company dashboard page',
                'type' => 'dropdown'
            ]
        ];
    }

    public function getCompanyPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->property('companyPage');
        $this->companiesList = $this->page['userCompanies'] = $this->companiesListWithUrl();
        if (isset($this->companiesList) && count($this->companiesList) === 1) {
            return redirect($this->companiesList[0]->pageUrl);
        }

    }

    public function companiesListWithUrl()
    {
        $userCompaniesList = $this->user()->companies()->get();
        $companies = [];
        foreach ($userCompaniesList as $company) {
            $company['pageUrl'] = $this->controller->pageUrl($this->property('companyPage'),
                ['company' => $company->slug]);
            $companies[] = $company;
        }

        return $companies;
    }

    public function user()
    {
        if (!$user = \Auth::getUser()) {
            return null;
        }
        $user->touchLastSeen();
        return $user;
    }


}
