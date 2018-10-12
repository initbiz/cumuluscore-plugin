<?php namespace Initbiz\CumulusCore\Components;

use Cms\Classes\ComponentBase;
use Event;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\CumulusCore\Models\Cluster;
use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;
use Initbiz\CumulusCore\Repositories\ClusterRepository;

class Menu extends ComponentBase
{
    use \Initbiz\CumulusCore\Traits\CumulusComponentProperties;

    public $clusterRepository;

    public function componentDetails()
    {
        return [
            'name' => 'initbiz.cumuluscore::lang.menu.name',
            'description' => 'initbiz.cumuluscore::lang.menu.description'
        ];
    }

    public function onRun()
    {
        $this->clusterRepository = new ClusterRepository;
        //Building navigation
        $this->page['menuEntries'] =$this->getMenuEntries();
    }

    public function getMenuEntries()
    {
        //TODO: Refactor spaghetti code
        $clusterFeatures = $this->clusterRepository->getClusterFeaturesCodes($this->property('clusterSlug'));
        $menuEntries= [];
        $pagesWithMenuItem = $this->getPagesWithComponent('menuItem');
        foreach ($pagesWithMenuItem as $page) {
            $component = $this->getComponentPropertiesFromPage($page, 'menuItem');
            if ($component['cumulusModule'] === "none"
                || in_array($component['cumulusModule'], $clusterFeatures, true)) {
                $menuEntries[$component['menuItemTitle']] = CmsPage::url($page['fileName']);
            }
        }

        return $menuEntries;
    }


    public function getPagesWithComponent($componentName)
    {
        $theme = Theme::getActiveTheme();
        $pages = CmsPage::listInTheme($theme, true);
        $pagesWithComponent = [];
        foreach ($pages as $page) {
            if ($page->hasComponent($componentName)) {
                $pagesWithComponent[] = $page;
            }
        }
        return $pagesWithComponent;
    }

    public function getComponentPropertiesFromPage($page, $componentName)
    {
        foreach ($page['settings']['components'] as $tmpComponentName => $componentProperties) {
            $exp_key = explode(' ', $tmpComponentName);
            if ($exp_key[0] === $componentName) {
                return $page['settings']['components'][$tmpComponentName];
            }
        }
    }
}
