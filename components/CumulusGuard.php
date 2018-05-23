<?php namespace Initbiz\CumulusCore\Components;

use Session;
use Cms\Classes\ComponentBase;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\CumulusCore\Repositories\ClusterRepository;

class CumulusGuard extends ComponentBase
{
    use \Initbiz\Cumuluscore\Traits\CumulusComponentProperties;

    public $clusterRepository;

    public function componentDetails()
    {
        return [
            'name' => 'initbiz.cumuluscore::lang.cumulus_guard.name',
            'description' => 'initbiz.cumuluscore::lang.cumulus_guard.description'
        ];
    }

    public function onRun()
    {
        $this->clusterRepository = new ClusterRepository;
        $clusterSlug = $this->property('clusterSlug');

        if (!$this->clusterRepository->canEnterCluster(
            Helpers::getUser()->id,
            $clusterSlug
        )) {
            $this->setStatusCode(403);
            return $this->controller->run('403');
        }

        $this->page['cluster'] = $clusterSlug;

        Session::put('initbiz.cumuluscore.currentClusterSlug', $clusterSlug);
    }
}
