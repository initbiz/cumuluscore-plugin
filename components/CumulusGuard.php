<?php namespace Initbiz\CumulusCore\Components;

use Cookie;
use Session;
use Cms\Classes\ComponentBase;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\CumulusCore\Repositories\ClusterRepository;

class CumulusGuard extends ComponentBase
{
    use \Initbiz\CumulusCore\Traits\CumulusComponentProperties;

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
        $clusterSlug = $this->property('clusterSlug');
        $this->clusterRepository = new ClusterRepository($clusterSlug);

        if (!$this->clusterRepository->canEnterCluster(
            Helpers::getUser()->id,
            $clusterSlug
        )) {
            $this->setStatusCode(403);
            return $this->controller->run('403');
        }

        $this->page['cluster'] = $clusterSlug;
        $this->page['clusterData'] = $this->clusterRepository->getCurrentCluster();

        Session::put('cumulus_clusterslug', $clusterSlug);
        Cookie::queue(Cookie::forever('cumulus_clusterslug', $clusterSlug));
    }
}
