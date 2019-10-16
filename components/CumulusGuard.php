<?php namespace Initbiz\CumulusCore\Components;

use Cms\Classes\ComponentBase;
use Initbiz\CumulusCore\Classes\Helpers;

class CumulusGuard extends ComponentBase
{
    use \Initbiz\CumulusCore\Traits\CumulusComponentProperties;

    public function componentDetails()
    {
        return [
            'name' => 'initbiz.cumuluscore::lang.cumulus_guard.name',
            'description' => 'initbiz.cumuluscore::lang.cumulus_guard.description'
        ];
    }

    public function onRun()
    {
        $cluster = Helpers::getClusterFromUrlParam($this->property('clusterUniq'));

        if (!$cluster) {
            return $this->controller->run('404');
        }

        Helpers::setCluster($cluster);

        $this->page['cluster'] = Helpers::getCluster();
    }
}
