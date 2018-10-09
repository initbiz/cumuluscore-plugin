<?php namespace Initbiz\CumulusCore\Classes;

use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;
use Initbiz\CumulusCore\Models\Module;
use Initbiz\CumulusCore\Models\Cluster;
use Auth;

class Helpers
{
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

    public static function clusterId($slug)
    {
        return Cluster::where('slug', $slug)->first()->cluster_id;
    }

    public static function getFileListToDropdown()
    {
        return CmsPage::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }
}
