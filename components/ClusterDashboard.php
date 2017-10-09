<?php namespace InitBiz\Cumuluscore\Components;

use Cms\Classes\ComponentBase;
use RainLab\User\Models\User as Users;

class ClusterDashboard extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Cluster dashboard',
            'description' => 'Show cluster dashboard'
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
                'title'       => 'Cluster slug',
                'description' => 'Slug of cluster that dashboard is going to be shown',
                'type' => 'string',
                'default' => '{{ :cluster }}'
            ]
        ];
    }
}
