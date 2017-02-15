<?php namespace InitBiz\CumulusCore\Components;

use Cms\Classes\ComponentBase;
use Auth;

class CumulusGuard extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Cumulus guard',
            'description' => ''
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        if (!$this->canEnterCompany()) {
            $this->setStatusCode(403);
            return $this->controller->run('403');
        }
        $this->page['company'] = $this->param('company');
    }

    protected function canEnterCompany()
    {
        //TODO: move to model scope
        return $this->user()->companies()->whereSlug($this->param('company'))->first()? true : false;
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
