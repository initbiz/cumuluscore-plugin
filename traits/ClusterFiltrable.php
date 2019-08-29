<?php namespace Initbiz\CumulusCore\Traits;

use Cookie;
use Session;
use Initbiz\CumulusCore\Models\Cluster;

/**
 * Use this trait in models that you want to filter using cluster_slug property
 * This won't work in backend - we have neither cumulus_clusterslug in session nor cookie
 */
trait ClusterFiltrable
{
    /*
     * clusterSlug to be used in models methods
     */
    protected $clusterSlug;

    /**
     * prepare clusterSlug property using data in cookie set in CumulusGuard Component
     * @return boolean this->clusterSlug set or not
     */
    protected function prepareClusterSlug()
    {
        if ($this->clusterSlug) {
            return $this->clusterSlug;
        }

        //It is considered secure as cookies created by October are encrypted
        //More info: https://octobercms.com/docs/services/request-input#cookies

        //Get current cluster from session, if it fails, get from cookie
        $this->clusterSlug = Session::get('cumulus_clusterslug', Cookie::get('cumulus_clusterslug'));

        //Cookie may be unset, if so return false
        if (!$this->clusterSlug) {
            return false;
        }
        return $this->clusterSlug;
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

        $this->prepareClusterSlug();

        return $query->where($attribute, $this->clusterSlug);
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

        $this->prepareClusterSlug();

        $cluster = Cluster::where('slug', $this->clusterSlug)->first();

        return $query->where($attribute, $cluster->id);
    }

    /**
     * get validation rule of unique in cluster
     * @param  string $attribute   attribute that have to be unique
     * @param  string $table       table name, by default will be loaded with $this->table
     * @param  string $columnName  column with cluster slug
     * @return string              the rule string
     */
    public function clusterUnique($attribute, $table = null, $columnName = 'cluster_slug')
    {
        $this->prepareClusterSlug();

        if (!$this->clusterSlug) {
            return '';
        }

        $rule  = 'unique:';

        if ($table) {
            $rule .= $table;
        } else {
            $rule .= $this->table;
        }

        $rule .= ','.$attribute.',NULL,'.$attribute.','.$columnName.','.$this->clusterSlug;

        // For example: unique:initbiz_exampleplugin_table,email_address,NULL,email_address,cluster_slug,example-cluster
        // It will check if the cluster has the email_address unique or not

        return $rule;
    }
}
