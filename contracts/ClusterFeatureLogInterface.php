<?php namespace Initbiz\CumulusCore\Contracts;

interface ClusterFeatureLogInterface extends RepositoryInterface
{

    /**
     * refresh
     * @param  string  $clusterSlug
     * @param  array  $features array of cluster's features
     * @return
     */
    public function registerClusterFeatures(string $clusterSlug, array $features);
}
