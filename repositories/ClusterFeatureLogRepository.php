<?php namespace Initbiz\CumulusCore\Repositories;

use Db;
use Event;
use Exception;
use Initbiz\CumulusCore\Contracts\ClusterFeatureLogInterface;

class ClusterFeatureLogRepository implements ClusterFeatureLogInterface
{
    public $clusterRepository;
    public $clusterFeatureLogModel;
    public $userRepository;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->clusterFeatureLogModel = new \Initbiz\CumulusCore\Models\ClusterFeatureLog;
        $this->clusterRepository = new ClusterRepository();
    }

    /**
     * {@inheritdoc}
     */
    public function all($columns = array('*'))
    {
        return $this->clusterFeatureLogModel->get($columns);
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $perPage = 15, $columns = array('*'))
    {
        return $this->clusterFeatureLogModel->paginate($perPage, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return $this->clusterFeatureLogModel->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $data, $id, $attribute="id")
    {
        $cluster = $this->clusterModel->where($attribute, '=', $id)->first();
        foreach ($data as $key => $value) {
            $cluster->$key = $value;
        }
        $cluster->save();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id)
    {
        return $this->clusterFeatureLogModel->destroy($id);
    }

    /**
     * {@inheritdoc}
     */
    public function find(int $id, $columns = array('*'))
    {
        return $this->clusterFeatureLogModel->find($id, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(string $field, $value, $columns = array('*'))
    {
        return $this->clusterFeatureLogModel->where($field, '=', $value)->first($columns);
    }

    /**
     * {@inheritdoc}
     */
    public function getByRelationPropertiesArray(string $relationName, string $propertyName, array $array)
    {
        return $this->clusterFeatureLogModel->whereHas($relationName, function ($query) use ($propertyName, $array) {
            $query->whereIn($propertyName, $array);
        })->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getUsingArray(string $field, array $array)
    {
        $plans = $this->clusterFeatureLogModel->where($field, array_shift($array));
        foreach ($array as $item) {
            $plans = $plans->orWhere($field, $item);
        }
        return $plans->get();
    }

    /**
     * {@inheritdoc}
     */
    public function registerClusterFeatures(string $clusterSlug, array $features)
    {
         $registredFeatures = $this->clusterRegisteredFeatures($clusterSlug, $features);
         $featuresToRegister = array_diff($features, $registredFeatures);
         foreach ($featuresToRegister as $feature) {
            $this->registerClusterFeature($clusterSlug, $feature);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function registerClusterFeature(String $clusterSlug, string $feature)
    {
        Db::beginTransaction();

        $state = Event::fire('initbiz.cumuluscore.registerClusterFeature', [$clusterSlug, $feature], true);
        if ($state === false) {
            Db::rollBack();
            //TODO: Create own Excetion class
            throw new Exception();
        }

        $data = [
               'cluster_slug' => $clusterSlug,
               'feature_code' => $feature,
               'action' => 'registered',
        ];
        $this->create($data);

        Db::commit();
    }

    /**
     * {@inheritdoc}
     */
    public function clusterRegisteredFeatures(string $clusterSlug)
    {
            return $this->clusterFeatureLogModel->clusterFiltered($clusterSlug)
                        ->registered()
                        ->get()
                        ->pluck('feature_code')
                        ->toArray();
    }
}
