<?php

use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class EnterClusterDashboardPageTest extends Ui2TestCase
{

    use CumulusDataProviders,
        CumulusHelpers;

    /**
     * @test *
     * @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function user_with_cluster_can_enter_clusters_dashboard($userData, $clusterData)
    {
        $this->signInToBackend()
            ->createUser($userData)
            ->createCluster($clusterData)
            ->activateUser($userData['email'])
            ->addUserToCluster($userData['email'], $clusterData['name'])
            ->hold(2)
            ->signInToFrontend($userData)
            ->hold(2)
            ->see('Menu');
    }

    /**
     * @test *
     * * @return void
     */
    public function user_with_two_clusters_can_enter_both_clusters_dashboard()
    {
        $user = $this->fakeUserData();
        $firstCluster = $this->fakeClusterData();
        $secondCluster = $this->fakeClusterData();
        $this->signInToBackend()
            ->createUser($user)
            ->createCluster($firstCluster)
            ->createCluster($secondCluster)
            ->activateUser($user['email'])
            ->addUserToCluster($user['email'], $firstCluster['name'])
            ->hold(1)
            ->addUserToCluster($user['email'], $secondCluster['name'])
            ->hold(1)
            ->signInToFrontend($user)
            ->seePageIs('/system/choose-cluster')
            ->findAndClickElement($firstCluster['name'], "//h2[contains(., '{$firstCluster['name']}')]")
            ->hold(1)
            ->see('Menu')
            ->seePageIs('/system/' . $this->slugify($firstCluster['name']) . '/dashboard')
            ->visit('/system/choose-cluster')
            ->findAndClickElement($secondCluster['name'], "//h2[contains(., '{$secondCluster['name']}')]")
            ->hold(1)
            ->see('Menu')
            ->seePageIs('/system/' . $this->slugify($secondCluster['name']) . '/dashboard');
    }


    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }
}