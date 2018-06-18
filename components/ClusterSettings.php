<?php namespace Initbiz\CumulusCore\Components;

use Cms\Classes\ComponentBase;
use Initbiz\CumulusCore\Repositories\ClusterRepository;

class ClusterSettings extends ComponentBase
{
    use \Initbiz\CumulusCore\Traits\CumulusComponentProperties;

    public function componentDetails()
    {
        return [
            'name' => 'initbiz.cumuluscore::lang.cluster_settings.name',
            'description' => 'initbiz.cumuluscore::lang.cluster_settings.description'
        ];
    }

    public function onRun()
    {
        $clusterRepository = new ClusterRepository;
        $this->page['clusterModules'] = $clusterRepository->getClusterModules($this->property('clusterSlug'));
    }
}
