<?php namespace InitBiz\CumulusCore\Classes;

use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;
use InitBiz\CumulusCore\Models\Module;

class Helpers
{
    public static function findComponentPage($componentAlias)
    {
        $theme = Theme::getActiveTheme();
        $pages = CmsPage::listInTheme($theme, true);

        foreach ($pages as $page) {
            if ($page->hasComponent($componentAlias)) {
                return $page;
            }
        }
    }

    public static function getPagesFilenames() {
        return CmsPage::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public static function getModulesList() {
        return Module::all()->lists('name', 'slug');
    }
}
