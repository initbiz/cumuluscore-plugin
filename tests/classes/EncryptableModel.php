<?php

namespace Initbiz\CumulusCore\Tests\Classes;

use Model;
use Initbiz\Cumuluscore\Models\Cluster;

class EncryptableModel extends Model
{
    use \Initbiz\CumulusCore\Traits\ClusterEncryptable;

    protected $guarded = ['*'];

    protected $clusterEncryptable = [
        'confidential_field'
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'initbiz_cumuluscore_encryptable_model';

    public $belongsTo = [
        'cluster' => [
            Cluster::class,
            'table' => 'initbiz_cumuluscore_clusters',
        ],
    ];

    public $attachOne = [
        'logo' => ['System\Models\File']
    ];
}
