<?php namespace Initbiz\CumulusCore\Classes;

use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;
use Initbiz\CumulusCore\Models\Module;
use Auth;

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

    public static function getUser()
    {
        if (!$user = Auth::getUser()) {
            return null;
        }

        $user->touchLastSeen();

        return $user;
    }
}
