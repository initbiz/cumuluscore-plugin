<?php namespace Initbiz\Cumuluscore\Repositories;
use Initbiz\Cumuluscore\Contracts\ClusterInterface;

class ClusterRepository implements ClusterInterface {

    public $clusterModel;
    public $planModel;
    public $userRepository;

    public function __construct() {
        $this->clusterModel = new \Initbiz\Cumuluscore\Models\Cluster;
        $this->planModel = new \Initbiz\Cumuluscore\Models\Plan;
        $this->userRepository = new UserRepository();
    }

    public function all($columns = array('*')) {
        return $this->clusterModel->get($columns);
    }

    public function paginate(int $perPage = 15, $columns = array('*')) {
        return $this->clusterModel->paginate($perPage, $columns);
    }

    public function create(array $data) {
        return $this->clusterModel->create($data);
    }

    public function update(array $data,int $id, $attribute="id") {
        return $this->clusterModel->where($attribute, '=', $id)->update($data);
    }

    public function delete(int $id) {
        return $this->clusterModel->destroy($id);
    }

    public function find(int $id, $columns = array('*')) {
        return $this->clusterModel->find($id, $columns);
    }

    public function findBy(string $field, $value, $columns = array('*')) {
        return $this->clusterModel->where($field, '=', $value)->first($columns);
    }

    public function canEnterCluster(int $userId, string $clusterSlug)
    {
        return $this->userRepository->find($userId)->clusters()->whereSlug($clusterSlug)->first()? true : false;
        //return Helpers::getUser()->clusters()->whereSlug($this->property('clusterSlug'))->first()? true : false;
    }

    public function canEnterModule(string $clusterSlug, string $moduleSlug)
    {
        return $this->clusterModel
                    ->whereSlug($clusterSlug)
                    ->first()
                    ->plan()
                    ->first()
                    ->modules()
                    ->whereSlug($moduleSlug)
                    ->first()
            ? true : false;
    }

    public function getClusterModules(string $clusterSlug)
    {
         return $this->findBy('slug', $clusterSlug)
            ->plan()
            ->first()
            ->modules()
             ->get();
    }


    public function getClusterModulesName(string $clusterSlug)
    {
        $current_cluster_modules = $this->getClusterModules($clusterSlug);
        if ($current_cluster_modules !== null) {
            $current_cluster_modules = $current_cluster_modules
                ->pluck('name')
                ->values()
                ->map(function ($item, $key) {
                    return str_slug($item);
                })
                ->toArray();
        } else {
            $current_cluster_modules = [];
        }
        return $current_cluster_modules;
    }


    public function addUserToCluster(int $userId, string $clusterSlug) {
        $cluster = $this->clusterModel->where('slug', $clusterSlug)->first();
        if ($cluster) {
            $this->userRepository->find($userId)->clusters()->add($cluster);
        }
    }

    public function addClusterToPlan(string $clusterSlug, string $planSlug) {
        $plan = $this->planModel->where('slug', $planSlug)->first();
        if ($plan) {
            $cluster = $this->clusterModel->where('slug', $clusterSlug)->first();

            return $cluster->plan()->associate($plan)->save();
        }
    }

}
