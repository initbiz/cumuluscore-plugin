<?php

namespace Initbiz\CumulusCore\Components;

use Event;
use Cms\Classes\ComponentBase;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\InitDry\Classes\Helpers as DryHelpers;

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
            'clusterUniq' => [
                'title' => 'initbiz.cumuluscore::lang.component_properties.cluster_uniq',
                'description' => 'initbiz.cumuluscore::lang.component_properties.cluster_uniq_desc',
                'type' => 'string',
                'default' => '{{ :cluster }}'
            ]
        ];
    }

    public function onRun()
    {
        $cluster = Helpers::getClusterFromUrlParam($this->property('clusterUniq'));

        if (!$cluster) {
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }

        $user = DryHelpers::getUser();

        if (!$user || !$user->canEnter($cluster)) {
            $this->setStatusCode(403);
            return $this->controller->run('403');
        }

        $firstVisit = empty($cluster->last_visited_at);
        Helpers::setCluster($cluster);

        if ($firstVisit) {
            Event::fire('initbiz.cumuluscore.firstClusterVisit', [$cluster, $user]);
        }

        $this->page['cluster'] = Helpers::getCluster();
    }
}
