<?php namespace  Initbiz\Cumuluscore\Repositories;

use Initbiz\CumulusCore\Contracts\UserInterface;

class UserRepository implements UserInterface {

    public $userModel;

    public function __construct() {
        $this->userModel = new \Rainlab\User\Models\User;
    }

    public function all($columns = array('*')) {
        return $this->userModel->get($columns);
    }

    public function paginate(int $perPage = 15, $columns = array('*')) {
        return $this->userModel->paginate($perPage, $columns);
    }

    public function create(array $data) {
        return $this->userModel->create($data);
    }

    public function update(array $data,int $id, $attribute="id") {
        return $this->userModel->where($attribute, '=', $id)->update($data);
    }

    public function delete(int $id) {
        return $this->userModel->destroy($id);
    }

    public function find(int $id, $columns = array('*')) {
        return $this->userModel->find($id, $columns);
    }

    public function findBy(string $field, $value, $columns = array('*')) {
        return $this->userModel->where($field, '=', $value)->first($columns);
    }

    public function getUserClusterList(int $userId)
    {
        return $this->userModel
                    ->find($userId)
                    ->clusters()
                    ->get();
    }

}
