<?php namespace Initbiz\CumulusCore\Models;

use Model;
use Cms\Classes\Theme;
use Cms\Classes\Page as CmsPage;
use RainLab\Location\Models\Country;
use RainLab\User\Models\User as UserModel;

/**
 * Model
 */
class Cluster extends Model
{
    use \October\Rain\Database\Traits\Nullable;
    use \October\Rain\Database\Traits\Sluggable;
    // use \October\Rain\Database\Traits\Validation;

    protected $guarded = ['*'];

    /**
     * @var array Generate slugs for these attributes.
     */
    protected $slugs = ['slug' => 'name'];

    /**
     * Fields to be set as null when left empty
     * @var array
     */
    protected $nullable = [
        'name',
        'slug',
        'plan_id',
        'thoroughfare',
        'city',
        'phone',
        'country_id',
        'postal_code',
        'description',
        'email',
        'tax_number',
        'account_number'
    ];

    protected $fillable = [
        'name',
        'slug',
        'plan_id',
        'thoroughfare',
        'city',
        'phone',
        'country_id',
        'postal_code',
        'description',
        'email',
        'tax_number',
        'account_number'
    ];

    /*
     * Validation
     */
    //TODO: problems with auto assigning clusters. While saving model email is required, although it's not...
    // public $rules = [
    //     'name'   => 'required|between:4,255',
    //     'slug'        => 'between:4,100|unique:initbiz_cumuluscore_clusters',
    //     'email'       => 'between:6,255|email',
    // ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'initbiz_cumuluscore_clusters';

    public $belongsTo = [
        'plan' => [
            Plan::class,
            'table' => 'initbiz_cumuluscore_plans',
        ],
        'country' => [
            Country::class,
            'table' => 'rainlab_location_countries',
        ]
    ];

    public $hasMany = [
        'users' => [
            UserModel::class,
            'table' => 'users',
            'otherKey' => 'user_id'
        ]
    ];

    public $attachOne = [
        'logo' => ['System\Models\File']
    ];

    public function scopeApplyPlanFilter($query, $filtered)
    {
        return $query->whereHas('plan', function ($q) use ($filtered) {
            $q->whereIn('plan_id', $filtered);
        });
    }
}
