<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Initbiz\InitDry\Classes\Helpers;
use Initbiz\CumulusCore\Models\GeneralSettings;

class UserClustersList extends ComponentBase
{
    public $clustersList;

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
        $this->clustersList = $this->page['userClusters'] = $this->clustersListWithUrl();

        if (isset($this->clustersList) && count($this->clustersList) === 1) {
            return redirect($this->clustersList[0]->pageUrl);
        }
    }

    /**
     * Get list of the cluster with URLs injected
     *
     * @return array
     */
    public function clustersListWithUrl()
    {
        $user = Helpers::getUser();
        $userClustersList = $user->clusters()->get();

        if (GeneralSettings::get('enable_usernames_in_urls')) {
            $clusterUniq = 'username';
        } else {
            $clusterUniq = 'slug';
        }

        $clusters = [];
        foreach ($userClustersList as $cluster) {
            $cluster['pageUrl'] = $this->controller->pageUrl(
                $this->property('clusterDashboardPage'),
                ['cluster' => $cluster->$clusterUniq]
            );
            $clusters[] = $cluster;
        }

        return $clusters;
    }
}
