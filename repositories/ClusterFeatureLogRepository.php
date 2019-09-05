<?php namespace Initbiz\CumulusCore\Repositories;

use Db;
use Event;
use Exception;
use Initbiz\CumulusCore\Contracts\ClusterFeatureLogInterface;

class ClusterFeatureLogRepository implements ClusterFeatureLogInterface
{
    /**
     * {@inheritdoc}
     */
    public function registerClusterFeatures(string $clusterSlug, array $features)
    {
         $registredFeatures = $this->clusterRegisteredFeatures($clusterSlug, $features);
         $featuresToRegister = array_diff($features, $registredFeatures);
         foreach ($featuresToRegister as $feature) {
            $this->registerClusterFeature($clusterSlug, $feature);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function registerClusterFeature(String $clusterSlug, string $feature)
    {
        Db::beginTransaction();

        $state = Event::fire('initbiz.cumuluscore.registerClusterFeature', [$clusterSlug, $feature], true);
        if ($state === false) {
            Db::rollBack();
            //TODO: Create own Excetion class
            throw new Exception();
        }

        $data = [
               'cluster_slug' => $clusterSlug,
               'feature_code' => $feature,
               'action' => 'registered',
        ];
        $this->create($data);

        Db::commit();
    }

    /**
     * {@inheritdoc}
     */
    public function clusterRegisteredFeatures(string $clusterSlug): array
    {
            return $this->clusterFeatureLogModel->clusterFiltered($clusterSlug)
                        ->registered()
                        ->get()
                        ->pluck('feature_code')
                        ->toArray();
    }
}
