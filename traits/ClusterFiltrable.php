<?php namespace Initbiz\CumulusCore\Traits;

use Cookie;
use Session;

/**
 * Use this trait in models that you want to filter using cluster_slug property
 * This won't work in backend - we have neither cumulus_clusterslug in session nor cookie
 */
trait ClusterFiltrable
{
    /*
     * clusterSlug to be used in models methods
     */
    protected $clusterSlug;

    /**
     * prepare clusterSlug property using data in cookie set in CumulusGuard Component
     * @return boolean this->clusterSlug set or not
     */
    protected function prepareClusterSlug()
    {
        //It is considered secure as cookies created by October are encrypted
        //More info: https://octobercms.com/docs/services/request-input#cookies

        //Get current cluster from session, if it fails, get from cookie
        $clusterSlug = Session::get('cumulus_clusterslug', Cookie::get('cumulus_clusterslug'));

        //Cookie may be unset, if so return false
        if ($clusterSlug) {
            $this->clusterSlug = $clusterSlug;
            return true;
        }
        return false;
    }

    /**
     * get model filtered by cluster using cluster_slug property
     * @param  $query
     * @param  string $clusterSlug Cluster's slug
     * @return $query
     */
    public function scopeClusterFiltered($query, $clusterSlug = '')
    {
        if ($clusterSlug !== '') {
            return $query->where('cluster_slug', $clusterSlug);
        }

        if (!$this->clusterSlug) {
            $this->prepareClusterSlug();
        }

        return $query->where('cluster_slug', $this->clusterSlug);
    }
}
