<?php namespace Initbiz\Cumuluscore\Components;
use Cms\Classes\ComponentBase;
use Event;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\CumulusCore\Models\Cluster;
use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;
use Initbiz\Cumuluscore\Repositories\ClusterRepository;

class Menu extends ComponentBase
{
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
    public function defineProperties()
    {
        return [
            'clusterSlug' => [
                'title' => 'initbiz.cumuluscore::lang.menu.cluster_slug',
                'description' => 'initbiz.cumuluscore::lang.menu.cluster_slug_desc',
                'type' => 'string',
                'default' => '{{ :cluster }}'
            ]
        ];
    }
    public function getMenuEntries()
    {
        //TODO: Refactor spaghetti code
        $current_cluster_modules = $this->clusterRepository->getClusterModulesName($this->property('clusterSlug'));
//        dd($current_cluster_modules);
        $menuEntries= [];
        $pagesWithMenuItem = $this->getPagesWithComponent('menuItem');
        foreach ($pagesWithMenuItem as $page) {
            $component = $this->getComponentPropertiesFromPage($page, 'menuItem');
            if ($component['cumulusModule'] === "none"
                || in_array($component['cumulusModule'], $current_cluster_modules, true)) {
                $menuEntries[$component['menuItemTitle']] = CmsPage::url($page['fileName']);
            }

        }

//            dd($menuEntries);
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
//        dd($pagesWithComponent);
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