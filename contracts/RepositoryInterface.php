<?php namespace Initbiz\Cumuluscore\Contracts;

interface RepositoryInterface
{
    public function all($columns = array('*'));

    public function paginate(int $perPage = 15, $columns = array('*'));

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function find(int $id, $columns = array('*'));

    public function findBy(string $field, $value, $columns = array('*'));
}
