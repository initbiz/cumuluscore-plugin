<?php namespace InitBiz\CumulusCore\Classes;

use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;

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
}
