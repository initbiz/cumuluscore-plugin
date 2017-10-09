<?php namespace Initbiz\Cumuluscore\Components;

use Cms\Classes\ComponentBase;
use InitBiz\CumulusCore\Classes\Helpers;
use InitBiz\CumulusCore\Models\Cluster;
use InitBiz\CumulusCore\Models\Module;

class ModuleGuard extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Module Guard',
            'description' => 'Guard component that allows cluster enter the module'
        ];
    }

    public function defineProperties()
    {
        return [
            'clusterSlug' => [
                'title'       => 'Cluster slug',
                'description' => 'Slug of cluster that dashboard is going to be shown',
                'type' => 'string',
                'default' => '{{ :cluster }}'
            ],
            'cumulusModule' => [
                'description' => 'Pick module to restrict user access',
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
