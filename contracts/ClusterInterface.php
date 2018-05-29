<?php namespace Initbiz\CumulusCore\Contracts;

interface ClusterInterface extends RepositoryInterface
{
    public function canEnterCluster(int $userId, string $clusterSlug);

    public function canEnterModule(string $clusterSlug, string $moduleSlug);

    public function getClusterModules(string $clusterSlug);

    public function getClusterModulesName(string $clusterSlug);

    /**
     * Get users from clusters slugs array
     * @param  array  $clustersSlugs array of cluster slugs
     * @return Collection Users in clusters
     */
    public function getClustersUsers(array $clustersSlugs);
}
