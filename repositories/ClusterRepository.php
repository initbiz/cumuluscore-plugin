<?php namespace Initbiz\CumulusCore\Repositories;

use Event;
use Initbiz\CumulusCore\Contracts\ClusterInterface;

class ClusterRepository implements ClusterInterface
{
    public $clusterModel;

    public $userRepository;

    public $currentCluster;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $currentClusterSlug = '')
    {
        $this->clusterModel = new \Initbiz\CumulusCore\Models\Cluster;
        $this->userRepository = new UserRepository();
        if ($currentClusterSlug !== '') {
            $this->refreshCurrentCluster($currentClusterSlug);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function all($columns = array('*'))
    {
        return $this->clusterModel->get($columns);
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $perPage = 15, $columns = array('*'))
    {
        return $this->clusterModel->paginate($perPage, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return $this->clusterModel->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $data, $id, $attribute="cluster_id")
    {
        return $this->clusterModel->where($attribute, '=', $id)->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id)
    {
        return $this->clusterModel->destroy($id);
    }

    /**
     * {@inheritdoc}
     */
    public function find(int $id, $columns = array('*'))
    {
        return $this->clusterModel->find($id, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(string $field, $value, $columns = array('*'))
    {
        return $this->clusterModel->where($field, '=', $value)->first($columns);
    }

    /**
     * {@inheritdoc}
     */
    public function getByRelationPropertiesArray(string $relationName, string $propertyName, array $array)
    {
        return $this->clusterModel->whereHas($relationName, function ($query) use ($propertyName, $array) {
            $query->whereIn($propertyName, $array);
        })->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getUsingArray(string $field, array $array)
    {
        $clusters = $this->clusterModel->where($field, array_shift($array));
        foreach ($array as $item) {
            $clusters = $clusters->orWhere($field, $item);
        }
        return $clusters->get();
    }

    /**
     * {@inheritdoc}
     */
    public function canEnterCluster(int $userId, string $clusterSlug)
    {
        return $this->userRepository->find($userId)->clusters()->whereSlug($clusterSlug)->first()? true : false;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getClustersUsers(array $clustersSlugs)
    {
        $users = '';

        $clustersIds = $this->getUsingArray('slug', $clustersSlugs)->pluck('cluster_id')->toArray();

        $users = $this->userRepository->getByRelationPropertiesArray('clusters', 'initbiz_cumuluscore_clusters.cluster_id', $clustersIds);

        return $users;
    }

    /**
     * {@inheritdoc}
     */
    public function getClusterModules(string $clusterSlug)
    {
        $this->refreshCurrentCluster($clusterSlug);
        return $this->currentCluster
            ->plan()
            ->first()
            ->modules()
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getClusterModulesSlugs(string $clusterSlug)
    {
        $currentClusterModules = $this->getClusterModules($clusterSlug);

        $slugs = [];
        foreach ($currentClusterModules as $module) {
            $slugs[] = $module->slug;
        }
        return $slugs;
    }

    /**
     * {@inheritdoc}
     */
    public function addUserToCluster(int $userId, string $clusterSlug)
    {
        $this->refreshCurrentCluster($clusterSlug);
        if ($this->currentCluster) {
            $user = $this->userRepository->find($userId);

            Event::fire('initbiz.cumuluscore.addUserToCluster', [$user, $this->currentCluster]);

            $user->clusters()->add($this->currentCluster);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addClusterToPlan(string $clusterSlug, string $planSlug)
    {
        $this->planRepository = new PlanRepository();

        $plan = $this->planRepository->findBy('slug', $planSlug);
        if ($plan) {
            $this->refreshCurrentCluster($clusterSlug);

            Event::fire('initbiz.cumuluscore.addClusterToPlan', [$this->currentCluster, $plan]);

            $this->currentCluster->plan()->associate($plan);
            $this->currentCluster->save();
        }
    }

    /**
     * {@inheritdoc}
     */
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
     * {@inheritdoc}
     */
    public function refreshCurrentCluster(string $clusterSlug)
    {
        //It's good place to enable caching fo clusters
        if (!isset($this->currentCluster) || $this->currentCluster->slug !== $clusterSlug) {
            $this->currentCluster = $this->findBy('slug', $clusterSlug);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentCluster()
    {
        if (!$this->currentCluster) {
            return [];
        }

        $currentCluster = $this->currentCluster->toArray();
        if ($logo = $this->currentCluster->logo()->first()) {
            $currentCluster['logo'] = $logo->toArray();
        }

        return $currentCluster;
    }
}
