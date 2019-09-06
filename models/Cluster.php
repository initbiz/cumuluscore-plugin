<?php namespace Initbiz\CumulusCore\Models;

use Db;
use Event;
use Model;
use Cms\Classes\Theme;
use RainLab\User\Models\User;
use Cms\Classes\Page as CmsPage;
use Initbiz\InitDry\Classes\Helpers;
use RainLab\Location\Models\Country;
use RainLab\User\Models\User as UserModel;
use Initbiz\Cumuluscore\Models\ClusterFeatureLog;

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
        'featureLogs' => [
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

    public function afterSave()
    {
        $plan = $this->plan()->first();

        if ($plan && $plan->features) {
            $features = (array) $plan->features;
            $this->registerFeatures($features);
        }
    }

    /**
     * Check if cluster can enter feature
     *
     * @param string $featureCode
     * @return boolean
     */
    public function canEnterFeature(string $featureCode)
    {
        return in_array($featureCode, $this->features) ? true : false;
    }

    /**
     * Get cluster's features basing on its plan
     *
     * @return array
     */
    public function getFeaturesAttribute():array
    {
        $features = $this->plan()->first()->features;

        if (!isset($features) || $features === "0") {
            return [];
        }

        return (array) $features;
    }

    /**
     * Get cluster's registered features of the cluster
     *
     * @return array
     */
    public function getRegisteredFeaturesAttribute(): array
    {
        return ClusterFeatureLog::clusterFiltered($this->slug)
                                ->registered()
                                ->get()
                                ->pluck('feature_code')
                                ->toArray();
    }

    /**
     * Register feature for the cluster
     *
     * @param string $feature
     * @return void
     */
    public function registerFeature(string $feature)
    {
        Db::beginTransaction();

        $state = Event::fire('initbiz.cumuluscore.registerClusterFeature', [$this, $feature], true);
        if ($state === false) {
            Db::rollBack();
            //TODO: Create own Excetion class
            throw new Exception();
        }

        $logEntry = new ClusterFeatureLog();
        $logEntry->cluster_slug = $this->slug;
        $logEntry->feature_code = $feature;
        $logEntry->action = 'registered';
        $logEntry->save();

        Db::commit();
    }

    /**
     * Register cluster features in bulk
     *
     * @param array $features
     * @return void
     */
    public function registerFeatures(array $features)
    {
         $featuresToRegister = array_diff($features, $this->registered_features);
         foreach ($featuresToRegister as $feature) {
            $this->registerFeature($feature);
        }
    }
}

