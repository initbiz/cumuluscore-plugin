<?php namespace InitBiz\Cumuluscore\Components;

use Cms\Classes\ComponentBase;
use RainLab\User\Models\User as Users;

class ClusterDashboard extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'initbiz.cumuluscore::lang.cluster_dashboard.name',
            'description' => 'initbiz.cumuluscore::lang.cluster_dashboard.description'
        ];
    }

    public function onRun()
    {
        $this->page['workersNumber'] = Users::whereHas('clusters', function ($query) {
            $query->where('slug', $this->property('clusterSlug'));
        })->count();
    }

    public function defineProperties()
    {
        return [
            'clusterSlug' => [
                'title' => 'initbiz.cumuluscore::lang.cluster_dashboar.cluster_slug',
                'description' => 'initbiz.cumuluscore::lang.cluster_dashboar.cluster_slug_desc',
                'type' => 'string',
                'default' => '{{ :cluster }}'
            ]
        ];
    }
}
