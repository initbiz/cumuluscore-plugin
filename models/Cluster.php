<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Models;

use Db;
use Event;
use Model;
use Carbon\Carbon;
use RainLab\User\Models\User;
use October\Rain\Database\Builder;
use RainLab\Location\Models\Country;
use Initbiz\CumulusCore\Classes\ClusterKey;
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
    use \October\Rain\Database\Factories\HasFactory;

    protected $guarded = ['*'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'last_visited_at',
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
        'website',
        'last_visited_at',
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
        'website',
        'last_visited_at',
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
            'key' => 'cluster_id',
            'otherKey' => 'id',
        ]
    ];

    public $attachOne = [
        'logo' => ['System\Models\File']
    ];

    /**
     * Workaround for beforeRestore event being dispatched twice
     * To be removed when Laravel/October fixes the issue
     *
     * @var boolean
     */
    private $keyRestored = false;

    public function afterCreate()
    {
        ClusterKey::put($this->slug);
    }

    public function beforeSave()
    {
        $oldCluster = self::with('plan')->where('id', $this->id)->first();
        if ($oldCluster && $oldPlan = $oldCluster->getPlan()) {
            $plan = $this->getPlan();
            if ($oldPlan->id !== $plan->id) {
                Event::fire('initbiz.cumuluscore.planChanged', [$this, $oldPlan, $plan]);
            }
        }
    }

    public function afterSave()
    {
        $plan = $this->getPlan();

        if ($plan && $plan->features) {
            $features = (array) $plan->features;
            $this->refreshRegisteredFeatures($features);
        }
    }

    public function afterDelete()
    {
        ClusterKey::softDelete($this->slug, $this->deleted_at);
        $this->keyRestored = false;
    }

    public function beforeRestore()
    {
        if (!$this->keyRestored) {
            ClusterKey::restore($this->slug, $this->deleted_at);
            $this->keyRestored = true;
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
     * Filter clusters that can access specified feature
     *
     * @param Builder $query
     * @param string $feature
     * @return Builder
     */
    public function scopeWithAccessToFeature(Builder $query, string $feature): Builder
    {
        return $query->whereHas('plan', function ($q) use ($feature) {
            $q->where('features', 'like', '%"' . $feature . '"%');
        });
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
        if (in_array($featureCode, $this->features, true)) {
            return true;
        }

        if ((strlen($featureCode) > 1) && ends_with($featureCode, '*')) {
            $featureCodeWithoutAsterisk = substr($featureCode, 0, -1);
            foreach ($this->features as $feature) {
                if (starts_with($feature, $featureCodeWithoutAsterisk)) {
                    return true;
                }
            }
        }

        return false;
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
        $plan = $this->getPlan();

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
        $featureLogs = ClusterFeatureLog::clusterIdFiltered($this->id)->get()->groupBy('feature_code');
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
        $logEntry->cluster_id = $this->id;
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
        $logEntry->cluster_id = $this->id;
        $logEntry->feature_code = $feature;
        $logEntry->action = 'deregistered';
        $logEntry->save();

        Db::commit();
    }

    // Helpers

    /**
     * Internal helper to get plan instance
     *
     * @return Plan
     */
    public function getPlan()
    {
        if (isset($this->plan)) {
            return $this->plan;
        }

        return $this->plan = $this->plan()->first();
    }

    /**
     * Set last visited at to now
     *
     * @return void
     */
    public function touchLastVisited()
    {
        $this->update(['last_visited_at' => Carbon::now()]);
    }
}
