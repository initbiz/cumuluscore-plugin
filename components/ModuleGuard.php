<?php namespace Initbiz\CumulusCore\Components;

use Cms\Classes\ComponentBase;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\CumulusCore\Models\Module;
use Initbiz\CumulusCore\Repositories\ClusterRepository;

class ModuleGuard extends ComponentBase
{
    use \Initbiz\CumulusCore\Traits\CumulusComponentProperties;

    public $clusterRepository;

    public function componentDetails()
    {
        return [
            'name' => 'initbiz.cumuluscore::lang.module_guard.name',
            'description' => 'initbiz.cumuluscore::lang.module_guard.description'
        ];
    }

    public function defineProperties()
    {
        return $this->defineClusterSlug() +
        [
            'cumulusModule' => [
                'title' => 'initbiz.cumuluscore::lang.module_guard.cumulus_module',
                'description' => 'initbiz.cumuluscore::lang.module_guard.cumulus_module_desc',
                'type'        => 'dropdown'
            ]
        ];
    }

    public function onRun()
    {
        $this->clusterRepository = new ClusterRepository;
        if (!$this->clusterRepository->canEnterModule($this->property('clusterSlug'), $this->property('cumulusModule'))) {
            $this->setStatusCode(403);
            return $this->controller->run('403');
        }
    }

    public function getCumulusModuleOptions()
    {
        //TODO: get title from lang
        return Helpers::getModulesList();
    }
}
