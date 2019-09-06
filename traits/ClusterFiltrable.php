<?php namespace Initbiz\CumulusCore\Traits;

use Cookie;
use Session;
use Initbiz\CumulusCore\Models\Cluster;
use Initbiz\CumulusCore\Classes\Helpers;

/**
 * Use this trait in models that you want to filter using cluster_slug property
 * This won't work in backend - we have neither cumulus_clusterslug in session nor cookie
 */
trait ClusterFiltrable
{
    /*
     * cluster to be used in models methods
     */
    protected $clusterToFilter;

    /**
     * prepare cluster property using data in cookie set in CumulusGuard Component
     * @return boolean this->cluster set or not
     */
    protected function prepareClusterToFilter()
    {
        if ($this->clusterToFilter) {
            return $this->clusterToFilter;
        }

        //It is considered secure as cookies created by October are encrypted
        //More info: https://octobercms.com/docs/services/request-input#cookies

        $this->clusterToFilter = Helpers::getCluster();

        // Retrieving cluster to filtered failed, return false
        if (!$this->clusterToFilter) {
            return false;
        }

        return $this->clusterToFilter;
    }

    /**
     * get model filtered by value in specified attribute
     * @param  $query
     * @param  string $value value to filter data by
     * @return $query
     */
    public function scopeClusterFiltered($query, $value = '', $attribute = 'cluster_slug')
    {
        if ($value !== '') {
            return $query->where($attribute, $value);
        }

        $this->prepareClusterToFilter();

        return $query->where($attribute, $this->clusterToFilter->slug);
    }

    /**
     * get model filtered by value in specified attribute
     * @return $query
     */
    public function scopeClusterIdFiltered($query, $value = '', $attribute = 'cluster_id')
    {
        if ($value !== '') {
            return $query->clusterFiltered($value, $attribute);
        }

        $this->prepareClusterToFilter();

        $cluster = Cluster::where('slug', $this->clusterToFilter->slug)->first();

        return $query->where($attribute, $cluster->id);
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
        $this->prepareClusterToFilter();

        if (!$this->clusterToFilter) {
            return '';
        }

        $rule  = 'unique:';

        if ($table) {
            $rule .= $table;
        } else {
            $rule .= $this->table;
        }

        $rule .= ','.$attribute.',NULL,'.$attribute.','.$columnName.','.$this->clusterToFilter->slug;

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
        $this->prepareClusterToFilter();

        if (!$this->clusterToFilter) {
            return '';
        }

        $rule  = 'unique:';

        if ($table) {
            $rule .= $table;
        } else {
            $rule .= $this->table;
        }

        $rule .= ','.$attribute.',NULL,'.$attribute.','.$columnName.','.$this->clusterToFilter->id;

        // For example: unique:initbiz_exampleplugin_table,email_address,NULL,email_address,cluster_slug,12
        // It will check if the cluster has the email_address unique or not

        return $rule;
    }
}
