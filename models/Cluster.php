<?php

namespace Initbiz\CumulusCore\Models;

use Db;
use Event;
use Model;
use RainLab\User\Models\User;
use RainLab\Location\Models\Country;
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
        'account_number',
        'website'
    ];

    protected $fillable = [
        'name',
        'username',
        'thoroughfare',
        'city',
        'phone',
        'country_id',
        'postal_code',
        'description',
        'email',
        'tax_number',
        'account_number',
        'website'
    ];

    /*
     * Validation
     */
    public $rules = [
        'name'      => 'required|between:1,255',
        'email'     => 'nullable|between:6,255|email',
        'logo'      => 'nullable|image',
    ];

    protected $jsonable = ['additional_data'];

    /**
     * Ensure slugs are unique when trashed items present
     *
     * @var bool
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
            User::class,
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

    public function beforeSave()
    {
        $oldCluster = Self::with('plan')->where('id', $this->id)->first();
        if ($oldCluster && $oldPlan = $oldCluster->plan()->first()) {
            $plan = $this->plan()->first();
            if ($oldPlan->id !== $plan->id) {
                Event::fire('initbiz.cumuluscore.planChanged', [$this, $oldPlan, $plan]);
            }
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

    // Scopes

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


    /**
     * Check if cluster can enter feature
     *
     * @param string $featureCode
     * @return bool
     */
    public function canEnterFeature(string $featureCode): bool
    {
        $can = $this->hasFeature($featureCode);

        Event::fire('initbiz.cumuluscore.canEnterFeature', [$this, $featureCode, &$can]);

        return $can;
    }

    /**
     * Check if cluster has access to the feature using
     * feature code or regex with asterisk
     *
     * @param string $featureCode
     * @return boolean
     */
    public function hasFeature(string $featureCode): bool
    {
        $has = false;
        if ((strlen($featureCode) > 1) && ends_with($featureCode, '*')) {
            $featureCode2 = substr($featureCode, 0, -1);
            foreach ($this->features as $feature) {
                if (starts_with($feature, $featureCode2)) {
                    $has = true;
                    break;
                }
            }
        } else {
            $has = in_array($featureCode, $this->features) ? true : false;
        }

        return $has;
    }

    /**
     * Check if cluster can enter any of features supplied
     *
     * @param mixed     $featureCodes any object that can be casted to array
     * @return bool
     */
    public function canEnterAnyFeature($featureCodes): bool
    {
        $featureCodes = (array) $featureCodes;
        $can = false;

        foreach ($featureCodes as $featureCode) {
            if ($this->canEnterFeature($featureCode)) {
                $can = true;
                break;
            }
        }

        return $can;
    }

    /**
     * Get cluster's features basing on its plan
     *
     * @return array
     */
    public function getFeaturesAttribute(): array
    {
        $plan = $this->plan()->first();

        if ($plan) {
            $features = $plan->features;
            if (!isset($features) || $features === "0") {
                return [];
            }

            return (array) $features;
        }

        return [];
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
     * Get whole address at once
     *
     * @return string address
     */
    public function getAddressAttribute()
    {
        // TODO: Format customizable, or get by locale somehow
        return $this->postal_code . ' ' . $this->city . ', ' . $this->thoroughfare;
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
