<?php

namespace Initbiz\CumulusCore\Traits;

trait CumulusComponentProperties
{
    public function defineClusterUniq()
    {
        return [
            'clusterUniq' => [
                'title' => 'initbiz.cumuluscore::lang.component_properties.cluster_uniq',
                'description' => 'initbiz.cumuluscore::lang.component_properties.cluster_uniq_desc',
                'type' => 'string',
                'default' => '{{ :cluster }}'
            ]
        ];
    }

    public function defineProperties()
    {
        return $this->defineClusterUniq();
    }
}
