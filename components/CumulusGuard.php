<?php namespace InitBiz\CumulusCore\Components;

use Cms\Classes\ComponentBase;
use Auth;

class CumulusGuard extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'initbiz.cumuluscore::lang.cumulus_guard.name',
            'description' => 'initbiz.cumuluscore::lang.cumulus_guard.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'clusterSlug' => [
                'title' => 'initbiz.cumuluscore::lang.cumulus_guard.cluster_slug',
                'description' => 'initbiz.cumuluscore::lang.cumulus_guard.cluster_slug_desc',
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
