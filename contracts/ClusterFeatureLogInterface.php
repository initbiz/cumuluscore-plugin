<?php namespace Initbiz\CumulusCore\Contracts;

interface ClusterFeatureLogInterface extends RepositoryInterface
{

    /**
     * Register clusters` features
     * @param  string  $clusterSlug
     * @param  array  $features array of cluster's features
     * @return void
     */
    public function registerClusterFeatures(string $clusterSlug, array $features);


    /**
     * Register clusters' feature
     * @param  string    $clusterSlug
     * @param  string $feature   feature code
     * @return void
     */
    public function registerClusterFeature(String $clusterSlug, string $feature);

    /**
     * Get clusters' registered features
     * @param  string $clusterSlug
     * @return array  array of clusters' registered features codes
     */
    public function clusterRegisteredFeatures(string $clusterSlug):array;
}
