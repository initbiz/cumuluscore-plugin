<?php

declare(strict_types=1);

namespace Initbiz\Cumuluscore\Models;

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
        'cluster_id',
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
            'key' => 'id',
            'otherKey' => 'cluster_id',
        ]
    ];
}
