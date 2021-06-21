<?php

namespace Initbiz\CumulusCore\Traits;

use App;
use Initbiz\CumulusCore\Classes\Helpers;

/**
 * Use this trait in models that you want to filter using cluster_slug property
 * This won't work in backend - we have neither cumulus_clusterslug in session nor cookie
 */
trait ClusterFiltrable
{

    /**
     * get currently logged in cluster, works only in the frontend
     * or in backend for models that has cluster relation defined
     * @return Cluster|null
     */
    protected function getCluster()
    {
        if (!App::runningInBackend()) {
            if ($cluster = Helpers::getCluster()) {
                return $cluster;
            }
        } else {
            if ($cluster = $this->cluster()->first()) {
                return $cluster;
            }
        }
    }

    /**
     * get model filtered by value in specified attribute
     * will return empty collection if cluster not set
     * @param  $query
     * @param  string $value value to filter data by
     * @return $query
     */
    public function scopeClusterFiltered($query, $value = '', $attribute = 'cluster_slug')
    {
        if ($value !== '') {
            return $query->where($attribute, $value);
        }

        $cluster = $this->getCluster();

        $clusterSlug = $cluster->slug ?? '_empty_cluster_slug';

        return $query->where($attribute, $clusterSlug);
    }

    /**
     * get model filtered by value in specified attribute
     * will return empty collection if cluster not set
     * @param  $query
     * @param  string $value value to filter data by
     * @return $query
     */
    public function scopeClusterIdFiltered($query, $value = '', $attribute = 'cluster_id')
    {
        if ($value !== '') {
            return $query->clusterFiltered($value, $attribute);
        }

        $cluster = $this->getCluster();

        $clusterId = $cluster->id ?? 0;

        return $query->where($attribute, $clusterId);
    }

    /**
     * get validation rule of unique in cluster using its slug
     * @param  string $attribute   attribute that have to be unique
     * @param  string $table       table name, by default will be loaded with $this->table
     * @param  string $columnName  column with cluster slug
     * @return string              the rule string
     */
    public function clusterUnique($attribute, $table = null, $columnName = 'cluster_slug')
    {
        $cluster = $this->getCluster();

        if (!$cluster) {
            return '';
        }

        $rule  = 'unique:';

        if ($table) {
            $rule .= $table;
        } else {
            $rule .= $this->table;
        }

        $rule .= ',' . $attribute . ',NULL,' . $attribute . ',' . $columnName . ',' . $cluster->slug;

        // For example: unique:initbiz_exampleplugin_table,email_address,NULL,email_address,cluster_slug,example-cluster
        // It will check if the cluster has the email_address unique or not

        return $rule;
    }

    /**
     * get validation rule of unique in cluster using its id
     * @param  string $attribute   attribute that have to be unique
     * @param  string $table       table name, by default will be loaded with $this->table
     * @param  string $columnName  column with cluster id
     * @return string              the rule string
     */
    public function clusterIdUnique($attribute, $table = null, $columnName = 'cluster_id')
    {
        $cluster = $this->getCluster();

        if (!$cluster) {
            return '';
        }

        $rule  = 'unique:';

        if ($table) {
            $rule .= $table;
        } else {
            $rule .= $this->table;
        }

        $rule .= ',' . $attribute . ',NULL,' . $attribute . ',' . $columnName . ',' . $cluster->id;

        // For example: unique:initbiz_exampleplugin_table,email_address,NULL,email_address,cluster_slug,12
        // It will check if the cluster has the email_address unique or not

        return $rule;
    }

    /**
     * Returns true if the current cluster's id is in model's cluster_id property
     * or cluster's slug in cluster_slug property
     * or relation called cluster returns the same cluster as the current one
     *
     * for other logic, you have to override the method in the model
     *
     * @return bool
     */
    public function clusterCanManage()
    {
        $can = false;

        $cluster = Helpers::getCluster();

        if (
            !empty($cluster) &&
            (
                (!empty($this->cluster_id) && (int) $this->cluster_id === $cluster->id) ||
                (!empty($this->cluster_slug) && $this->cluster_slug === $cluster->slug) ||
                (!empty($this->cluster) && $this->cluster()->first()->id === $cluster->id))
        ) {
            $can = true;
        }

        return $can;
    }
}
