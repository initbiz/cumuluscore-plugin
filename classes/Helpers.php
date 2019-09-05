<?php namespace Initbiz\CumulusCore\Classes;

use Cookie;
use Session;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\CumulusCore\Models\GeneralSettings;
use Initbiz\CumulusCore\Repositories\ClusterRepository;

class Helpers
{
    public static function getCluster()
    {
        $clusterSlug = Session::get('cumulus_clusterslug', Cookie::get('cumulus_clusterslug'));

        $cluster = Cluster::where('slug', $clusterSlug)->first();

        return $cluster;
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

    public static function getClusterUsernameFromUrlParam($param)
    {
        $clusterUsername = '';

        if (GeneralSettings::get('enable_usernames_in_urls')) {
            $clusterUsername = $param;
        } else {
            $cluster = self::getClusterFromUrlParam($param);
            $clusterUsername = $cluster->username;
        }

        return $clusterUsername;
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

    public static function clusterUsername($slug)
    {
        return Cluster::where('slug', $slug)->first()->username;
    }
}
