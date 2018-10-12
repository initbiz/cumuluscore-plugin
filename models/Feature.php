<?php namespace Initbiz\CumulusCore\Models;

use Model;

/**
 * Model
 */
class Module extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [
        'code' => 'unique:initbiz_cumuluscore_features'
    ];

    public $fillable = ['code', 'name'];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'initbiz_cumuluscore_features';

    public $belongsToMany = [
        'plans' => [
            Plan::class,
            'table' => 'initbiz_cumuluscore_plan_feature',
            'key'      => 'plan_id',
        ]
    ];
}
