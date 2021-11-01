<?php

namespace Initbiz\CumulusCore\Classes;

use App;
use Event;
use Cookie;
use Session;
use Validator;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\CumulusCore\Models\GeneralSettings;
use Initbiz\CumulusCore\Classes\ClusterEncrypter;
use Initbiz\InitDry\Classes\Helpers as DryHelpers;
use Initbiz\CumulusCore\Classes\ClusterCacheObject;

class Helpers
{
    /**
     * Get cluster object using session or cookie data
     *
     * @return Cluster
     */
    public static function getCluster()
    {
        $clusterCache = ClusterCacheObject::instance();
        $cluster = $clusterCache->getCluster();

        if (!$cluster) {
            return;
        }

        $user = DryHelpers::getUser();

        if ($user && $user->canEnter($cluster)) {
            return $cluster;
        }
    }

    /**
     * Set cluster object to session and cookie
     *
     * @param Cluster cluster to set
     */
    public static function setCluster(Cluster $cluster)
    {
        $currentCluster = self::getCluster();

        if ($currentCluster && $currentCluster->id === $cluster->id) {
            return;
        }

        $user = DryHelpers::getUser();

        if (!$user->canEnter($cluster)) {
            App::abort(403, 'Cannot access cluster');
        }

        Session::put('cumulus_clusterslug', $cluster->slug);
        Cookie::queue(Cookie::forever('cumulus_clusterslug', $cluster->slug));
    }

    /**
     * Remove current cluster slug from the session and cookie
     */
    public static function forgetCluster()
    {
        ClusterEncrypter::forgetInstance();

        Session::pull('cumulus_clusterslug');
        Cookie::queue(Cookie::forget('cumulus_clusterslug'));
    }

    /**
     * Get cluster object using parameter in URL
     *
     * @param string $param
     * @return Cluster
     */
    public static function getClusterFromUrlParam($param)
    {
        if (GeneralSettings::get('enable_usernames_in_urls')) {
            $cluster = Cluster::where('username', $param)->first();
        } else {
            $cluster = Cluster::where('slug', $param)->first();
        }

        return $cluster;
    }

    /**
     * Checks if $username is unique in database with firing blocking event
     * The clusterSlug parameter is required for the unique rule to know
     * which id to ignore in the DB table
     *
     * @param string $username
     * @param string $clusterSlug
     * @return boolean
     */
    public static function usernameUnique(string $username, string $clusterSlug)
    {
        $cluster = Cluster::where('slug', $clusterSlug)->first();

        $rules = [
            'username' => 'required|between:4,255|alpha_dash|unique:initbiz_cumuluscore_clusters,username,' . $cluster->id,
        ];

        $data = [
            'username' => $username,
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return false;
        }

        $state = Event::fire('initbiz.cumuluscore.usernameUnique', [$username, $clusterSlug], true);

        if ($state === false) {
            return false;
        }

        return true;
    }
}
