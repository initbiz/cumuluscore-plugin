<?php namespace Initbiz\CumulusCore\Contracts;

interface ClusterInterface extends RepositoryInterface
{
    /**
     * Check if user can enter cluster
     * @param  int    $userId      User's ID
     * @param  string $clusterSlug Cluster's slug
     * @return boolean             can or cannot enter cluster
     */
    public function canEnterCluster(int $userId, string $clusterSlug);

    /**
     * Check if cluster can enter feature
     * @param  string $clusterSlug Cluster's slug
     * @param  string $featureCode  Feature's slug
     * @return boolean             can or cannot enter feature
     */
    public function canEnterFeature(string $clusterSlug, string $featureCode);

    /**
     * Get current cluster's features
     * @param  string $clusterSlug Cluster slug
     * @return array  cluster features codes
     */
    public function getClusterFeatures(string $clusterSlug);

    /**
     * Get users from clusters slugs array
     * @param  array  $clustersSlugs array of cluster slugs
     * @return Collection Users in clusters
     */
    public function getClustersUsers(array $clustersSlugs);

    /**
     * Get array of clusters' plans (lot of clusters, one method)
     * @param  array  $clustersSlugs array of cluster slugs
     * @return Collection Plans of clusters
     */
    public function getClustersPlans(array $clustersSlugs);

    /**
     * Set and get current cluster object for future method invokes to be more optimized
     * @param  string $clusterSlug cluster's slug
     * @return Cluster             current cluster
     */
    public function refreshCurrentCluster(string $clusterSlug);

    /**
     * Get current cluster, set using refreshCurrentCluster method
     * @return array array of current cluster data
     */
    public function getCurrentCluster();
}
