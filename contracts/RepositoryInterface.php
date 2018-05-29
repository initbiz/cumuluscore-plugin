<?php namespace Initbiz\Cumuluscore\Contracts;

interface RepositoryInterface
{
    /**
     * Get all records using columns
     * @param  array  $columns to get
     * @return Collection of records
     */
    public function all($columns = array('*'));

    /**
     * Get paginated records from model
     * @param  integer $perPage items per page
     * @param  array   $columns to get
     * @return Collection of records
     */
    public function paginate(int $perPage = 15, $columns = array('*'));

    /**
     * Create record using data
     * @param  array  $data data key => value with "column name" => value syntax
     */
    public function create(array $data);

    /**
     * Update record using data and its id
     * @param  array  $data data key => value with "column name" => value syntax
     * @param  int  $id of record to update
     */
    public function update(array $data, int $id);

    /**
     * Delete record
     * @param  int    $id record ID
     */
    public function delete(int $id);

    /**
     * Find record using ID, and get columns
     * @param  int    $id      ID of record
     * @param  array  $columns Columns to get
     */
    public function find(int $id, $columns = array('*'));

    /**
     * find by field
     * @param  string $field   unique field
     * @param  string|int $value   search for this
     * @param  array  $columns Columns to get
     */
    public function findBy(string $field, $value, $columns = array('*'));

    /**
     * Get records that has field in array
     * @param  string $field field name
     * @param  array  $array array with possibilities
     * @return Collection        Collection of records
     */
    public function getUsingArray(string $field, array $array);

    /**
     * Get records that has relation property in array - for example user has groups with ID [1,2,3] etc.
     * @param  string $relationName relation name - for example groups
     * @param  string $propertyName property name - for example group_id (sometimes need to add table name)
     * @param  array  $array        array of possibilities - for example [1,2,3]
     * @return Collection Collection of records
     */
    public function getByRelationPropertiesArray(string $relationName, string $propertyName, array $array);
}
