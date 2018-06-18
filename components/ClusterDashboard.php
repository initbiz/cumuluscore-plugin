<?php namespace Initbiz\CumulusCore\Components;

use Cms\Classes\ComponentBase;
use RainLab\User\Models\User as Users;

class ClusterDashboard extends ComponentBase
{
    use \Initbiz\CumulusCore\Traits\CumulusComponentProperties;

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
}
