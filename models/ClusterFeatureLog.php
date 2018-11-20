<?php namespace Initbiz\Cumuluscore\Models;

use Model;

/**
 * ClusterFeatureRegistration Model
 */
class ClusterFeatureLog extends Model
{
    use \Initbiz\CumulusCore\Traits\ClusterFiltrable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'initbiz_cumuluscore_cluster_feature_logs';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'cluster_slug',
        'feature_code',
        'action',
    ];

    public $timestamps = false;

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'cluster' => [
            Cluster::class,
            'table' => 'initbiz_cumuluscore_clusters',
        ]
    ];

    /**
     * Scope a query to only include registered features
     */
    public function scopeRegistered($query)
    {
        return $query->where('action', 'registered');
    }}
