<?php namespace Initbiz\CumulusCore\Models;

use Model;
use Initbiz\CumulusCore\Classes\FeatureManager;

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
    public $rules = [];

    public $fillable = ['name', 'slug', 'features'];

    protected $slugs = ['slug' => 'name'];

    protected $jsonable = ['features'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'initbiz_cumuluscore_plans';

    public $hasMany = [
        'clusters' => [
            Cluster::class,
            // 'key' => 'plan_id'
        ]
    ];

    public function getFeaturesOptions()
    {
        $featureManager = FeatureManager::instance();
        $featuresOptions = $featureManager->getFeaturesOptions();
        return $featuresOptions;
    }
}
