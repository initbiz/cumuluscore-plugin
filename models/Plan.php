<?php namespace Initbiz\CumulusCore\Models;

use Model;

/**
 * Model
 */
class Plan extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /*
     * Validation
     */
    public $rules = [
    ];

    public $fillable = ['plan_id', 'name', 'slug'];

    public $primaryKey = 'plan_id';

    /**
     * @var string The database table used by the model.
     */
    public $table = 'initbiz_cumuluscore_plans';

    public $belongsToMany = [
        'modules' => [
            Module::class,
            'table' => 'initbiz_cumuluscore_plan_module',
            'key'      => 'plan_id',
            'otherKey' => 'module_id'
        ]
    ];
    public $hasMany = [
        'clusters' => [
            Cluster::class
        ]
    ];
}
