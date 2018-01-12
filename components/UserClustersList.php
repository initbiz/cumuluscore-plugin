<?php namespace Initbiz\CumulusCore\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\Cumuluscore\Repositories\UserRepository;

class UserClustersList extends ComponentBase
{
    public $clustersList;
    public $userRepository;

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
        $this->userRepository = new UserRepository;
        $this->clustersList = $this->page['userClusters'] = $this->clustersListWithUrl();

        if (isset($this->clustersList) && count($this->clustersList) === 1) {
            return redirect($this->clustersList[0]->pageUrl);
        }
    }

    public function clustersListWithUrl()
    {
        $userClustersList = $this->userRepository->getUserClusterList(Helpers::getUser()->id);
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
}

