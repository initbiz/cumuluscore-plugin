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
use Initbiz\CumulusCore\Classes\Exceptions\RegisterFeatureException;
use Initbiz\CumulusCore\Classes\Exceptions\DeregisterFeatureException;

/**
 * Model
 */
class Cluster extends Model
{
    use \October\Rain\Database\Traits\Nullable;
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Validation;

    protected $guarded = ['*'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

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
    public $rules = [
        'name'      => 'required|between:1,255',
        'slug'      => 'between:1,100',
        'username'  => 'between:1,100',
        'email'     => 'nullable|between:6,255|email',
    ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /**
     * Ensure slugs are unique when trashed items present
     *
     * @var boolean
     */
    protected $allowTrashedSlugs = true;

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

    public $belongsToMany = [
        'users' => [
            UserModel::class,
            'table' => 'initbiz_cumuluscore_cluster_user',
            'order' => 'name',
        ]
    ];
    
    public $hasMany = [
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

    public function scopeApplyTrashedFilter($query, $type)
    {
        switch ($type) {
            case '1':
                return $query->withTrashed();
            case '2':
                return $query->onlyTrashed();
            default:
                return $query;
        }
    }

    public function afterSave()
    {
        $plan = $this->plan()->first();

        if ($plan && $plan->features) {
            $features = (array) $plan->features;
            $this->refreshRegisteredFeatures($features);
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
        $featureLogs = ClusterFeatureLog::clusterFiltered($this->slug)->get()->groupBy('feature_code');
        $features = [];

        foreach ($featureLogs as $feature_code => $group) {
            $newestElement = $group->sortByDesc('timestamp')->first();
            if ($newestElement->action === 'registered') {
                $features[] = $feature_code;
            }
        }

        return $features;
    }

    /**
     * Refresh registered cluster features,
     * register those to register and deregister those to deregister
     *
     * @param array $features of the current plan, all not included will be deregistered
     * @return void
     */
    public function refreshRegisteredFeatures(array $features)
    {
        $currentRegisteredFeatures = $this->registered_features;

        $featuresToRegister = array_diff($features, $currentRegisteredFeatures);
        $featuresToDeregister = array_diff($currentRegisteredFeatures, $features);

        foreach ($featuresToRegister as $feature) {
           $this->registerFeature($feature);
        }

        foreach ($featuresToDeregister as $feature) {
           $this->deregisterFeature($feature);
        }
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
            Db::rollback();
            throw new RegisterFeatureException();
        }

        $logEntry = new ClusterFeatureLog();
        $logEntry->cluster_slug = $this->slug;
        $logEntry->feature_code = $feature;
        $logEntry->action = 'registered';
        $logEntry->save();

        Db::commit();
    }

    /**
     * Deregister feature for the cluster
     *
     * @param string $feature
     * @return void
     */
    public function deregisterFeature(string $feature)
    {
        Db::beginTransaction();

        $state = Event::fire('initbiz.cumuluscore.deregisterClusterFeature', [$this, $feature], true);
        if ($state === false) {
            Db::rollback();
            throw new DeregisterFeatureException();
        }

        $logEntry = new ClusterFeatureLog();
        $logEntry->cluster_slug = $this->slug;
        $logEntry->feature_code = $feature;
        $logEntry->action = 'deregistered';
        $logEntry->save();

        Db::commit();
    }

}

