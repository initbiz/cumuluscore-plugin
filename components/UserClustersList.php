<?php namespace InitBiz\CumulusCore\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;

class UserClustersList extends ComponentBase
{
    public $clusterDashboardPage;
    public $clustersList;

    public $implement = [
        'InitBiz.CumulusCore.Behaviors.ComponentBehavior'
    ];

    public function componentDetails()
    {
        return [
            'name' => 'Clusters list',
            'description' => 'Component showing all clusters that user is assigned to'
        ];
    }

    public function defineProperties()
    {
        return [
            'clusterDashboardPage' => [
                'title' => 'Cluster dashboard page',
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
