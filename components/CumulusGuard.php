<?php namespace InitBiz\CumulusCore\Components;

use Cms\Classes\ComponentBase;
use Auth;

class CumulusGuard extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Cumulus guard',
            'description' => 'Component checking if user can enter cluster page'
        ];
    }

    public function defineProperties()
    {
        return [
            'clusterSlug' => [
                'title'       => 'Cluster slug',
                'description' => 'Slug of cluster that dashboard is going to be shown',
                'type' => 'string',
                'default' => '{{ :cluster }}'
            ]
        ];
    }

    public function onRun()
    {
        if (!$this->canEnterCluster()) {
            $this->setStatusCode(403);
            return $this->controller->run('403');
        }
        $this->page['cluster'] = $this->property('clusterSlug');
    }

    protected function canEnterCluster()
    {
        //TODO: move to model scope
        return $this->user()->clusters()->whereSlug($this->property('clusterSlug'))->first()? true : false;
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
