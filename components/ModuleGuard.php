<?php namespace Initbiz\Cumuluscore\Components;

use Cms\Classes\ComponentBase;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\CumulusCore\Models\Module;

class ModuleGuard extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'initbiz.cumuluscore::lang.module_guard.name',
            'description' => 'initbiz.cumuluscore::lang.module_guard.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'clusterSlug' => [
                'title' => 'initbiz.cumuluscore::lang.module_guard.cluster_slug',
                'description' => 'initbiz.cumuluscore::lang.module_guard.cluster_slug_desc',
                'type' => 'string',
                'default' => '{{ :cluster }}'
            ],
            'cumulusModule' => [
                'title' => 'initbiz.cumuluscore::lang.module_guard.cumulus_module',
                'description' => 'initbiz.cumuluscore::lang.module_guard.cumulus_module_desc',
                'type'        => 'dropdown'
            ]
        ];
    }

    public function onRun()
    {
        if (!$this->canEnterModule($this->property('clusterSlug'), $this->property('cumulusModule'))) {
            $this->setStatusCode(403);
            return $this->controller->run('403');
        }
    }

    public function getCumulusModuleOptions()
    {
        //TODO: get title from lang
        return Helpers::getModulesList();
    }

    public function canEnterModule($clusterSlug, $moduleSlug)
    {
        return Cluster::whereSlug($clusterSlug)
                        ->first()
                        ->plan()
                        ->first()
                        ->modules()
                        ->whereSlug($moduleSlug)
                        ->first()
            ? true : false;
    }
}
