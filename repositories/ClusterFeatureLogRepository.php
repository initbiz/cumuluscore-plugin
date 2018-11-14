<?php namespace Initbiz\CumulusCore\Repositories;

use Db;
use Event;
use Initbiz\CumulusCore\Contracts\ClusterFeatureLogInterface;

class ClusterFeatureLogRepository implements ClusterFeatureLogInterface
{
    public $clusterRepository;
    public $clusterFeatureLogModel;
    public $userRepository;

    public function __construct()
    {
        $this->clusterFeatureLogModel = new \Initbiz\CumulusCore\Models\ClusterFeatureLog;
        $this->clusterRepository = new ClusterRepository();
    }

    public function all($columns = array('*'))
    {
        return $this->clusterFeatureLogModel->get($columns);
    }

    public function paginate(int $perPage = 15, $columns = array('*'))
    {
        return $this->clusterFeatureLogModel->paginate($perPage, $columns);
    }

    public function create(array $data)
    {
        return $this->clusterFeatureLogModel->create($data);
    }

    public function update(array $data, $id, $attribute="id")
    {
        return $this->clusterFeatureLogModel->where($attribute, '=', $id)->update($data);
    }

    public function delete(int $id)
    {
        return $this->clusterFeatureLogModel->destroy($id);
    }

    public function find(int $id, $columns = array('*'))
    {
        return $this->clusterFeatureLogModel->find($id, $columns);
    }

    public function findBy(string $field, $value, $columns = array('*'))
    {
        return $this->clusterFeatureLogModel->where($field, '=', $value)->first($columns);
    }

    public function getByRelationPropertiesArray(string $relationName, string $propertyName, array $array)
    {
        return $this->clusterFeatureLogModel->whereHas($relationName, function ($query) use ($propertyName, $array) {
            $query->whereIn($propertyName, $array);
        })->get();
    }

    public function getUsingArray(string $field, array $array)
    {
        $plans = $this->clusterFeatureLogModel->where($field, array_shift($array));
        foreach ($array as $item) {
            $plans = $plans->orWhere($field, $item);
        }
        return $plans->get();
    }

    public function registerClusterFeatures(int $clusterId, array $features)
    {
         $registredFeatures = $this->clusterRegisteredFeatures($clusterId, $features);
         $featuresToRegister = array_diff($features, $registredFeatures);
         foreach ($featuresToRegister as $feature) {
            $this->registerClusterFeature($clusterId, $feature);
        }
    }

    public function registerClusterFeature(int $clusterId, string $feature)
    {
        Db::beginTransaction();

        $state = Event::fire('initbiz.cumuluscore.beforeRegisterClusterFeatures', [$clusterId, $feature], true);
        if ($state === false) {
            Db::rollBack();
            return;
        }

        $data = [
               'cluster_id' => $clusterId,
               'feature_code' => $feature,
               'action' => 'registered',
        ];
        $this->create($data);

        Db::commit();
    }

    public function clusterRegisteredFeatures(int $clusterId, array $features)
    {
            return $this->clusterFeatureLogModel->clusterFiltered($clusterId, 'cluster_id')
                        ->where('action', 'registered')
                        ->get()->pluck('feature_code')
                        ->toArray();
    }

}
