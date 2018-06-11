<?php namespace Initbiz\Cumuluscore\Repositories;

use Event;
use Initbiz\Cumuluscore\Contracts\PlanInterface;

class PlanRepository implements PlanInterface
{
    public $clusterRepository;
    public $planModel;
    public $userRepository;

    public function __construct()
    {
        $this->planModel = new \Initbiz\Cumuluscore\Models\Plan;
    }

    public function all($columns = array('*'))
    {
        return $this->planModel->get($columns);
    }

    public function paginate(int $perPage = 15, $columns = array('*'))
    {
        return $this->planModel->paginate($perPage, $columns);
    }

    public function create(array $data)
    {
        return $this->planModel->create($data);
    }

    public function update(array $data, int $id, $attribute="id")
    {
        return $this->planModel->where($attribute, '=', $id)->update($data);
    }

    public function delete(int $id)
    {
        return $this->planModel->destroy($id);
    }

    public function find(int $id, $columns = array('*'))
    {
        return $this->planModel->find($id, $columns);
    }

    public function findBy(string $field, $value, $columns = array('*'))
    {
        return $this->planModel->where($field, '=', $value)->first($columns);
    }

    public function getByRelationPropertiesArray(string $relationName, string $propertyName, array $array)
    {
        return $this->planModel->whereHas($relationName, function ($query) use ($propertyName, $array) {
            $query->whereIn($propertyName, $array);
        })->get();
    }

    public function getUsingArray(string $field, array $array)
    {
        $plans = $this->planModel->where($field, array_shift($array));
        foreach ($array as $item) {
            $plans = $plans->orWhere($field, $item);
        }
        return $plans->get();
    }


    public function getPlansUsers(array $plansSlugs)
    {
        $this->clusterRepository = new ClusterRepository();

        $users = '';

        $plansIds = $this->getUsingArray('slug', $plansSlugs)->pluck('plan_id')->toArray();

        $clusters = $this->clusterRepository->getByRelationPropertiesArray('plan', 'initbiz_cumuluscore_plans.plan_id', $plansIds);

        $clustersSlugs = $clusters->pluck('slug')->toArray();

        $users = $this->clusterRepository->getClustersUsers($clustersSlugs);

        return $users;
    }
}
