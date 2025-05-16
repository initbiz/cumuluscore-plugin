<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Classes;

use Cookie;
use Session;
use October\Rain\Support\Singleton;
use Initbiz\CumulusCore\Models\Cluster;

/**
 * Class created to cache cluster object for a single session time
 * to reduce queries to DB
 */
class ClusterCacheObject extends Singleton
{
    protected $cluster;

    protected function init()
    {
        $this->cluster = $this->fetchCluster();
    }

    public function setCluster(Cluster $cluster)
    {
        $this->cluster = $cluster;
    }

    public function getCluster()
    {
        if (!isset($this->cluster)) {
            $this->cluster = $this->fetchCluster();
        }

        return $this->cluster;
    }

    protected function fetchCluster()
    {
        $clusterSlug = Session::get('cumulus_clusterslug', Cookie::get('cumulus_clusterslug'));

        return Cluster::with('plan')->where('slug', $clusterSlug)->first();
    }
}
