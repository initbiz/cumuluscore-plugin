<?php namespace Initbiz\CumulusCore\Models;

use Model;
use October\Rain\Database\Collection;
use Initbiz\CumulusCore\Classes\FeatureManager;

class Plan extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Nullable;

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /*
     * Validation
     */
    public $rules = [];

    public $fillable = [
        'name',
        'slug',
        'features',
        'priority',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $slugs = [
        'slug' => 'name',
    ];

    protected $jsonable = [
        'features'
    ];

    /**
     * Fields to be set as null when left empty
     * @var array
     */
    protected $nullable = [
        'priority'
    ];

    /**
     * Ensure slugs are unique when trashed items present
     *
     * @var boolean
     */
    protected $allowTrashedSlugs = true;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'initbiz_cumuluscore_plans';

    public $hasMany = [
        'clusters' => [
            Cluster::class
        ]
    ];

    public $belongsToMany = [
        'related_plans' => [
            Plan::class,
            'table'     => 'initbiz_cumuluscore_related_plans',
            'key'       => 'plan_id',
            'otherKey'  => 'related_plan_id',
            'pivot'     => ['relation'],
        ]
    ];

    public function getFeaturesOptions()
    {
        return FeatureManager::instance()->getFeaturesOptions();
    }

    public function afterSave()
    {
        foreach ($this->clusters as $cluster) {
            if ($this->features === "0") {
                continue;
            }
            $cluster->registerFeatures($this->features);
        }
    }
    
    /**
     * Return plans that this plan can upgrade to
     *
     * @return Collection
     */
    public function plansToUpgrade()
    {
       return $this->related_plans()->where('relation', 'upgrade')->get();
    }

    /**
     * Returns true if the plan can be upgraded
     *
     * @return boolean
     */
    public function canUpgrade()
    {
        return ($this->plansToUpgrade()->count() > 0);
    }

    /**
     * Returns true if the plan can be upgraded to the $plan
     *
     * @return boolean
     */
    public function canUpgradeTo($plan)
    {
        foreach ($this->plansToUpgrade() as $planUpg) {
            if ($plan->id === $planUpg->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if the plan can be prolongated
     *
     * @return boolean
     */
    public function canProlongate()
    {
        return ($this->is_expiring && !$this->is_trial);
    }


    /**
     * Get users that belongs to clusters with this plan
     *
     * @return Collection
     */
    public function getUsersAttribute()
    {
        $users = collect();

        $clusters = $this->clusters()->get();

        foreach ($clusters as $cluster ) {
            $users->concat($cluster->users()->get());
        }
        
        return $users->unique();
    }
}

