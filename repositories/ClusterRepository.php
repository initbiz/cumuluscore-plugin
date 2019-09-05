<?php namespace Initbiz\CumulusCore\Repositories;

use Lang;
use Event;
use Validator;
use RainLab\User\Models\User;
use Initbiz\CumulusCore\Models\Plan;
use October\Rain\Exception\ValidationException;
use Initbiz\CumulusCore\Contracts\ClusterInterface;
use Initbiz\CumulusCore\Repositories\ClusterFeatureLogRepository;

class ClusterRepository implements ClusterInterface
{

    /**
     * {@inheritdoc}
     */
    public function canEnterFeature(string $clusterSlug, string $featureCode)
    {
        $this->refreshCurrentCluster($clusterSlug);

        $clusterFeatures = $this->getClusterFeatures($clusterSlug);

        $can = in_array($featureCode, $clusterFeatures) ? true : false;

        return $can;
    }

    /**
     * {@inheritdoc}
     */
    public function getClusterFeatures(string $clusterSlug):array
    {
        $this->refreshCurrentCluster($clusterSlug);

        $clusterFeatures = $this->currentCluster->plan()->first()->features;

        if (!isset($clusterFeatures) || $clusterFeatures === "0") {
            $clusterFeatures = [];
        }

        $clusterFeatures = (array) $clusterFeatures;
        return $clusterFeatures;
    }

    /**
     * {@inheritdoc}
     */
    public function addUserToCluster(int $userId, string $clusterSlug)
    {
        $this->refreshCurrentCluster($clusterSlug);
        if ($this->currentCluster) {
            $user = User::find($userId);

            Event::fire('initbiz.cumuluscore.addUserToCluster', [$user, $this->currentCluster]);

            $user->clusters()->syncWithoutDetaching($this->currentCluster);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addClusterToPlan(string $clusterSlug, string $planSlug)
    {
        $plan = Plan::where('slug', $planSlug)->first();
        if ($plan) {
            $this->refreshCurrentCluster($clusterSlug);

            $this->currentCluster->plan()->associate($plan);
            $this->currentCluster->save();

            Event::fire('initbiz.cumuluscore.addClusterToPlan', [$this->currentCluster, $plan]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function refreshCurrentCluster(string $clusterSlug)
    {
        //It's good place to enable caching fo clusters
        if (!isset($this->currentCluster) || $this->currentCluster->slug !== $clusterSlug) {
            $this->currentCluster = $this->findBy('slug', $clusterSlug);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentCluster()
    {
        if (!$this->currentCluster) {
            return [];
        }

        $currentCluster = $this->currentCluster->toArray();
        if ($logo = $this->currentCluster->logo()->first()) {
            $currentCluster['logo'] = $logo->toArray();
        }

        return $currentCluster;
    }

    /**
     * {@inheritdoc}
     */
    public function usernameUnique(string $username, string $clusterSlug)
    {
        $this->refreshCurrentCluster($clusterSlug);

        $rules = [
            'username' => 'required|between:4,255|alpha_dash|unique:initbiz_cumuluscore_clusters,username,' . $this->currentCluster->id,
        ];

        $data = [
            'username' => $username,
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return false;
        }

        $state = Event::fire('initbiz.cumuluscore.usernameUnique', [$username, $clusterSlug], true);

        if ($state === false) {
            return false;
        }

        return true;
    }
}
