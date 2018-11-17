<?php namespace Initbiz\CumulusCore\Classes;

use Auth;
use Cookie;
use Session;
use Cms\Classes\Theme;
use Cms\Classes\Page as CmsPage;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\CumulusCore\Models\GeneralSettings;
use Initbiz\CumulusCore\Repositories\ClusterRepository;

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

    public static function getClusterSlugFromUrlParam($param)
    {
        $clusterSlug = '';

        if (GeneralSettings::get('enable_usernames_in_urls')) {
            $cluster = self::getClusterFromUrlParam($param);
            $clusterSlug = $cluster->slug;
        } else {
            $clusterSlug = $param;
        }

        return $clusterSlug;
    }

    public static function getClusterFromUrlParam($param)
    {
        $findBy = '';
        if (GeneralSettings::get('enable_usernames_in_urls')) {
            $findBy = 'username';
        } else {
            $findBy = 'slug';
        }

        $clusterRepository = new ClusterRepository();
        $cluster = $clusterRepository->findBy($findBy, $param);

        return $cluster;
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
