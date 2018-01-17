<?php

use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class UserWithClusterAccessPagesTest extends Ui2TestCase {

    use Initbiz\CumulusCore\Traits\CumulusTestHelpers;
    use Initbiz\CumulusCore\Traits\CumulusDataProviders;

    /**
     * @test *
     * * @return void
     */
    public function user_with_cluster_cannot_enter_another_cluster_dashboard_page()
    {
        $firstClusterData = $this->fakeClusterData();
        $secondClusterData = $this->fakeClusterData();
        $userData = $this->fakeUserData();
        $secondClusterSlug = $this->slugify($secondClusterData['name']);
        $this->signInToBackend()
             ->createUser($userData)
             ->activateUser($userData['email'])
             ->createCluster($firstClusterData)
             ->createCluster($secondClusterData)
             ->addUserToCluster($userData['email'], $firstClusterData['name'])
             ->hold(1)
             ->signInToFrontend($userData)
             ->visit('/system/' .$secondClusterSlug.'/dashboard')
             ->hold(2)
             ->see('Forbidden');
    }

    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }
}
