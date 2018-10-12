<?php namespace Initbiz\CumulusCore\Models;

use Model;

class Plan extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sluggable;

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /*
     * Validation
     */
    public $rules = [ ];

    public $fillable = ['plan_id', 'name', 'slug'];

    public $primaryKey = 'plan_id';

    protected $slugs = ['slug' => 'name'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'initbiz_cumuluscore_plans';

    public $belongsToMany = [
        'features' => [
            Feature::class,
            'table' => 'initbiz_cumuluscore_plan_features',
            'key'      => 'plan_id',
            'otherKey' => 'feature_id'
        ]
    ];
    public $hasMany = [
        'clusters' => [
            Cluster::class,
            'key' => 'plan_id'
        ]
    ];
}
