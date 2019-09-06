<?php namespace Initbiz\CumulusCore\Components;

use Cookie;
use Session;
use Cms\Classes\ComponentBase;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\InitDry\Classes\Helpers as DryHelpers;

class CumulusGuard extends ComponentBase
{
    use \Initbiz\CumulusCore\Traits\CumulusComponentProperties;

    public function componentDetails()
    {
        return [
            'name' => 'initbiz.cumuluscore::lang.cumulus_guard.name',
            'description' => 'initbiz.cumuluscore::lang.cumulus_guard.description'
        ];
    }

    public function onRun()
    {
        $user = DryHelpers::getUser();

        if (!$user) {
            return $this->controller->run('403');
        }

        $cluster = Helpers::getClusterFromUrlParam($this->property('clusterUniq'));

        if (!$cluster) {
            return $this->controller->run('404');
        }

        if (!$user->canEnter($cluster)) {
            $this->setStatusCode(403);
            return $this->controller->run('403');
        }

        $this->page['cluster'] = $cluster;

        Session::put('cumulus_clusterslug', $cluster->slug);
        Cookie::queue(Cookie::forever('cumulus_clusterslug', $cluster->slug));
    }
}
