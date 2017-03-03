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
            'description' => ''
        ];
    }

    public function defineProperties()
    {
        return [
            'companyPage' => [
                'title' => 'Company page',
                'description' => 'Company page',
                'type' => 'dropdown',
                'group' => 'Links',
            ]
        ];
    }

    public function getCompanyPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->companyPage = $this->page['companyPage'] = $this->property('companyPage');
        $this->companiesList = $this->page['userCompanies'] = $this->companiesList();

        if (isset($this->companiesList) && $this->companiesList->count() === 1) {
            return redirect($this->controller->pageUrl($this->companyPage, ['company' => $this->companiesList->first()->slug]));
        }

    }


    public function companiesList()
    {
        return $this->user()->companies()->get();
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