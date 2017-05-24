<?php namespace InitBiz\CumulusCore\Components;

use Cms\Classes\ComponentBase;
use Auth;

class CumulusGuard extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Cumulus guard',
            'description' => 'Component checking if user can enter company'
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
            ]
        ];
    }

    public function onRun()
    {
        if (!$this->canEnterCompany()) {
            $this->setStatusCode(403);
            return $this->controller->run('403');
        }
        $this->page['company'] = $this->property('companySlug');
    }

    protected function canEnterCompany()
    {
        //TODO: move to model scope
        return $this->user()->companies()->whereSlug($this->property('companySlug'))->first()? true : false;
    }

    protected function user()
    {
        if (!$user = Auth::getUser()) {
            return null;
        }

        $user->touchLastSeen();

        return $user;
    }

}
