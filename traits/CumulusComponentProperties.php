<?php namespace Initbiz\CumulusCore\Traits;

trait CumulusComponentProperties
{
    public function defineClusterSlug()
    {
        return [
            'clusterSlug' => [
                'title' => 'initbiz.cumuluscore::lang.component_properties.cluster_slug',
                'description' => 'initbiz.cumuluscore::lang.component_properties.cluster_slug_desc',
                'type' => 'string',
                'default' => '{{ :cluster }}'
            ]
        ];
    }

    public function defineProperties()
    {
        return $this->defineClusterSlug();
    }
}
