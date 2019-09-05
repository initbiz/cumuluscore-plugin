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
