<?php namespace Initbiz\CumulusCore\Classes;

use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;
use Initbiz\CumulusCore\Models\Module;

class Helpers
{
    public static function getPagesFilenames()
    {
        return CmsPage::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public static function getModulesList()
    {
        return Module::all()->lists('name', 'slug');
    }
}
