<?php namespace Initbiz\CumulusCore\Classes;

use Event;
use Cookie;
use Session;
use Validator;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\CumulusCore\Models\GeneralSettings;

class Helpers
{
    /**
     * Get cluster object using session or cookie data
     * that are set by CumulusGuard
     *
     * @return Cluster
     */
    public static function getCluster()
    {
        $clusterSlug = Session::get('cumulus_clusterslug', Cookie::get('cumulus_clusterslug'));

        $cluster = Cluster::where('slug', $clusterSlug)->first();

        return $cluster;
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
