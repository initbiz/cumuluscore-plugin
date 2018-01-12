<?php namespace Initbiz\CumulusCore\Components;

use Initbiz\CumulusCore\Classes\Helpers;
use Cms\Classes\ComponentBase;
use Initbiz\CumulusCore\Repositories\ClusterRepository;

class CumulusGuard extends ComponentBase
{
    public $clusterRepository;

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
        $this->clusterRepository = new ClusterRepository;
        if (!$this->clusterRepository->canEnterCluster(Helpers::getUser()->id,
            $this->property('clusterSlug'))) {
            $this->setStatusCode(403);
            return $this->controller->run('403');
        }
        $this->page['cluster'] = $this->property('clusterSlug');
    }
}
