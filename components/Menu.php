<?php namespace InitBiz\Cumuluscore\Components;

use Cms\Classes\ComponentBase;
use Event;
use InitBiz\CumulusCore\Classes\Helpers;
use InitBiz\CumulusCore\Models\Cluster;
use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;

class Menu extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Menu Component',
            'description' => 'Component that renders menu based on menuItems component'
        ];
    }

    public function onRun()
    {
        //Building navigation
        $current_cluster_modules = Cluster::with('modules')
            ->where('slug', $this->property('clusterSlug'))
            ->first()
            ->modules()
            ->get()
            ->pluck('name')
            ->values()
            ->map(function ($item, $key) {
                return str_slug($item);
            })
            ->toArray();

        $menuEntries= [];
        $theme = Theme::getActiveTheme();
        $pages = CmsPage::listInTheme($theme, true);

        foreach ($pages as $page) {
            if ($page->hasComponent('menuItem')) {
                $component = '';
                foreach ($page['settings']['components'] as $componentName => $componentProperties) {
                    $exp_key = explode(' ', $componentName);
                    if ($exp_key[0] === 'menuItem') {
                        $component = $page['settings']['components'][$componentName];
                    }
                }
                if ($component['cumulusModule'] === "none"
                    || in_array($component['cumulusModule'], $current_cluster_modules, true)) {
                    $menuEntries[$component['menuItemTitle']] = CmsPage::url($page['fileName']);
                }
            }
        }
        $this->page['menuEntries'] =$menuEntries;
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
