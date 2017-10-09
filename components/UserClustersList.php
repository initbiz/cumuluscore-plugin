<?php namespace Initbiz\CumulusCore\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;

class UserClustersList extends ComponentBase
{
    public $clusterDashboardPage;
    public $clustersList;

    public $implement = [
        'Initbiz.CumulusCore.Behaviors.ComponentBehavior'
    ];

    public function componentDetails()
    {
        return [
            'name' => 'initbiz.cumuluscore::lang.user_clusters_list.name',
            'description' => 'initbiz.cumuluscore::lang.user_clusters_list.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'clusterDashboardPage' => [
                'title' => 'initbiz.cumuluscore::lang.user_clusters_list.cluster_dashboard_page',
                'description' => 'initbiz.cumuluscore::lang.user_clusters_list.cluster_dashboard_page_desc',
                'type' => 'dropdown'
            ]
        ];
    }

    public function getClusterDashboardPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->property('clusterDashboardPage');
        $this->clustersList= $this->page['userClusters'] = $this->clustersListWithUrl();

        if (isset($this->clustersList) && count($this->clustersList) === 1) {
            return redirect($this->clustersList[0]->pageUrl);
        }
    }

    public function clustersListWithUrl()
    {
        $userClustersList = $this->user()->clusters()->get();
        $clusters = [];
        foreach ($userClustersList as $cluster) {
            $cluster['pageUrl'] = $this->controller->pageUrl(
                $this->property('clusterDashboardPage'),
               ['cluster' => $cluster->slug]
            );
            $clusters[] = $cluster;
        }
        return $clusters;
    }


    public function user()
    {
        if (!$user = \Auth::getUser()) {
            return null;
        }
        $user->touchLastSeen();
        return $user;
    }
}
