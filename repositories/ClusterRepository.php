<?php namespace Initbiz\CumulusCore\Repositories;

use Event;
use Initbiz\CumulusCore\Contracts\ClusterInterface;

class ClusterRepository implements ClusterInterface
{
    public $clusterModel;

    public $userRepository;

    public $currentCluster;

    public function __construct(string $currentClusterSlug = '')
    {
        if ($currentClusterSlug) {
            $this->currentCluster = $this->findBy('slug', $currentClusterSlug);
        }
        $this->clusterModel = new \Initbiz\CumulusCore\Models\Cluster;
        $this->userRepository = new UserRepository();
    }

    public function all($columns = array('*'))
    {
        return $this->clusterModel->get($columns);
    }

    public function paginate(int $perPage = 15, $columns = array('*'))
    {
        return $this->clusterModel->paginate($perPage, $columns);
    }

    public function create(array $data)
    {
        return $this->clusterModel->create($data);
    }

    public function update(array $data, int $id, $attribute="id")
    {
        return $this->clusterModel->where($attribute, '=', $id)->update($data);
    }

    public function delete(int $id)
    {
        return $this->clusterModel->destroy($id);
    }

    public function find(int $id, $columns = array('*'))
    {
        $cluster = $this->clusterModel->find($id, $columns);
        if ($columns === array('*')) {
            $this->currentCluster = $cluster;
        }
        return $cluster;
    }

    public function findBy(string $field, $value, $columns = array('*'))
    {
        $cluster = $this->clusterModel->where($field, '=', $value)->first($columns);
        if ($columns === array('*')) {
            $this->currentCluster = $cluster;
        }
        return $cluster;
    }

    public function getByRelationPropertiesArray(string $relationName, string $propertyName, array $array)
    {
        return $this->clusterModel->whereHas($relationName, function ($query) use ($propertyName, $array) {
            $query->whereIn($propertyName, $array);
        })->get();
    }

    public function getUsingArray(string $field, array $array)
    {
        $clusters = $this->clusterModel->where($field, array_shift($array));
        foreach ($array as $item) {
            $clusters = $clusters->orWhere($field, $item);
        }
        return $clusters->get();
    }

    public function canEnterCluster(int $userId, string $clusterSlug)
    {
        return $this->userRepository->find($userId)->clusters()->whereSlug($clusterSlug)->first()? true : false;
    }

    public function canEnterModule(string $clusterSlug, string $moduleSlug)
    {
        $this->refreshCurrentCluster($clusterSlug);
        return $this->currentCluster
                    ->plan()
                    ->first()
                    ->modules()
                    ->whereSlug($moduleSlug)
                    ->first()
            ? true : false;
    }

    public function getClustersUsers(array $clustersSlugs)
    {
        $users = '';

        $clustersIds = $this->getUsingArray('slug', $clustersSlugs)->pluck('cluster_id')->toArray();

        $users = $this->userRepository->getByRelationPropertiesArray('clusters', 'initbiz_cumuluscore_clusters.cluster_id', $clustersIds);

        return $users;
    }

    public function getClusterModules(string $clusterSlug)
    {
        $cluster = $this->refreshCurrentCluster($clusterSlug);
        return $cluster
            ->plan()
            ->first()
            ->modules()
             ->get();
    }

    public function getClusterModulesSlugs(string $clusterSlug)
    {
        $currentClusterModules = $this->getClusterModules($clusterSlug);

        $slugs = [];
        foreach ($currentClusterModules as $module) {
            $slugs[] = $module->slug;
        }
        return $slugs;
    }

    public function addUserToCluster(int $userId, string $clusterSlug)
    {
        $cluster = $this->refreshCurrentCluster($clusterSlug);
        if ($cluster) {
            $user = $this->userRepository->find($userId);

            Event::fire('initbiz.cumuluscore.addUserToCluster', [$user, $cluster]);

            $user->clusters()->add($cluster);
        }
    }

    public function addClusterToPlan(string $clusterSlug, string $planSlug)
    {
        $this->planRepository = new PlanRepository();

        $plan = $this->planRepository->findBy('slug', $planSlug);
        if ($plan) {
            $cluster = $this->refreshCurrentCluster($clusterSlug);

            Event::fire('initbiz.cumuluscore.addClusterToPlan', [$cluster, $plan]);

            return $cluster->plan()->associate($plan)->save();
        }
    }

    public function getClustersPlans(array $clustersSlugs)
    {
        $plans = [];
        $clusters = $this->getUsingArray('slug', $clustersSlugs);

        foreach ($clusters as $cluster) {
            $plans[] = $cluster->plan()->first();
        }

        return collect($plans);
    }

    /**
     * Thanks to this method, there will be less queries to db - this will query only if differs
     * @param  string $clusterSlug cluster's slug
     * @return Cluster             current cluster
     */
    protected function refreshCurrentCluster($clusterSlug)
    {
        if (!isset($this->currentCluster) || $this->currentCluster->slug !== $clusterSlug) {
            $this->currentCluster = $this->findBy('slug', $clusterSlug);
        }
        return $this->currentCluster;
    }
}
