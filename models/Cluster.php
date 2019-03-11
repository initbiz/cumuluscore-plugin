<?php namespace Initbiz\CumulusCore\Models;

use Db;
use Event;
use Model;
use Cms\Classes\Theme;
use Cms\Classes\Page as CmsPage;
use RainLab\Location\Models\Country;
use RainLab\User\Models\User as UserModel;
use Initbiz\CumulusCore\Repositories\ClusterFeatureLogRepository;

/**
 * Model
 */
class Cluster extends Model
{
    use \October\Rain\Database\Traits\Nullable;
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\SoftDelete;
    // use \October\Rain\Database\Traits\Validation;

    protected $guarded = ['*'];

    protected $dates = ['deleted_at'];

    /**
     * @var array Generate slugs for these attributes.
     */
    protected $slugs = [
        'slug' => 'name',
        'username' => 'name',
    ];

    /**
     * Fields to be set as null when left empty
     * @var array
     */
    protected $nullable = [
        'name',
        'slug',
        'username',
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
        'username',
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
    //     'name'      => 'required|between:4,255',
    //     'slug'      => 'between:4,100|unique:initbiz_cumuluscore_clusters',
    //     'username'  => 'between:4,100|unique:initbiz_cumuluscore_clusters',
    //     'email'     => 'between:6,255|email',
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
        ],
        'clusterRegisteredFeatures' => [
            ClusterFeatureLog::class,
            'table' => 'initbiz_cumuluscore_cluster_feature_logs',
            'key' => 'cluster_slug',
            'otherKey' => 'slug',
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

    public function beforeSave()
    {
        /* This must be on model because every time the model is saved:
         * backend or repo or anywhere on create or update
         * there should be ability to check if for example
         * username is unique and if not, than return false, drop
         */
        Db::beginTransaction();
        $state = Event::fire('initbiz.cumuluscore.beforeClusterSave', [$this], true);
        if ($state === false) {
            Db::rollBack();
            return false;
        }
        Db::commit();
    }

    public function afterSave()
    {
        if ($this->plan && $this->plan->features) {
            $clusterFeatureLogRepository = new ClusterFeatureLogRepository();
            $clusterFeatureLogRepository->registerClusterFeatures($this->slug, (array)$this->plan->features);
        }
    }
}
