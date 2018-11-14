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
        $user = Helpers::getUser();

        if (!$user) {
            //throw? if user is not logged in, than guard should stop
            return false;
        }

        $cluster = Helpers::getClusterFromUrlParam($this->property('clusterUniq'));
        if (!$cluster) {
            //if cluster has not been found, what to do?
            return $this->controller->run('404');
        }
        $clusterSlug = $cluster->slug;

        $this->clusterRepository = new ClusterRepository($clusterSlug);

        if (!$this->clusterRepository->canEnterCluster($user->id, $clusterSlug)) {
            $this->setStatusCode(403);
            return $this->controller->run('403');
        }

        $this->page['cluster'] = $cluster;

        Session::put('cumulus_clusterslug', $clusterSlug);
        Cookie::queue(Cookie::forever('cumulus_clusterslug', $clusterSlug));
    }
}
