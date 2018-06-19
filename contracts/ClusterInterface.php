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
     * Check if cluster can enter module
     * @param  string $clusterSlug Cluster's slug
     * @param  string $moduleSlug  Module's slug
     * @return boolean             can or cannot enter module
     */
    public function canEnterModule(string $clusterSlug, string $moduleSlug);

    /**
     * Get current cluster's modules
     * @param  string $clusterSlug Cluster slug
     * @return array  cluster modules
     */
    public function getClusterModules(string $clusterSlug);

    /**
     * Get current cluster's modules slugs
     * @param  string $clusterSlug Cluster slug
     * @return array  cluster modules slugs
     */
    public function getClusterModulesSlugs(string $clusterSlug);

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
}
