<?php namespace InitBiz\CumulusCore\Models;

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
    ];

    public $fillable = ['module_id', 'name', 'slug'];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'initbiz_cumuluscore_modules';
    public $primaryKey = 'module_id';

    public $belongsToMany = [
        'companies' => [
            Company::class,
            'table' => 'initbiz_cumuluscore_company_module'
        ]
    ];
}