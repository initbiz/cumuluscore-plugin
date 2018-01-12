<?php namespace Initbiz\CumulusCore\Models;

use Model;
use RainLab\User\Models\UserGroup;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\CumulusCore\Models\Plan;

class Settings extends Model
{

    public $implement = [
        'System.Behaviors.SettingsModel'
    ];

    public $settingsCode = 'initbiz_cumulusproducts_settings';

    public $settingsFields = 'fields.yaml';

    public function getAutoAssignUserConcreteClusterOptions()
    {
        return Cluster::all()->pluck('full_name', 'slug')->toArray();
    }

    public function getGroupToAutoAssignUserOptions()
    {
        return UserGroup::all()->pluck('name', 'code')->toArray();
    }

    public function getAutoAssignClusterConcretePlanOptions()
    {
        return Plan::all()->pluck('name', 'plan_id')->toArray();
    }
}
