<?php namespace Initbiz\CumulusCore\Classes;

use Auth;
use Cookie;
use Session;
use Cms\Classes\Theme;
use Cms\Classes\Page as CmsPage;
use Initbiz\CumulusCore\Models\Cluster;

class Helpers
{
    public static function getUser()
    {
        if (!$user = Auth::getUser()) {
            return null;
        }

        $user->touchLastSeen();

        return $user;
    }

    public static function getCluster()
    {
        return Session::get('cumulus_clusterslug', Cookie::get('cumulus_clusterslug'));
    }

    public static function clusterId($slug)
    {
        return Cluster::where('slug', $slug)->first()->id;
    }

    public static function getFileListToDropdown()
    {
        return CmsPage::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public static function getPageUrl($pageCode, $theme)
    {
        $page = CmsPage::loadCached($theme, $pageCode);
        if (!$page) {
            return;
        }

        $url = CmsPage::url($page->getBaseFileName());

        return $url;
    }
}
