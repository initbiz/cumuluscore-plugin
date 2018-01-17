<?php

use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class AddUserToClusterTest extends Ui2TestCase {

    use Initbiz\CumulusCore\Traits\CumulusDataProviders;
    use Initbiz\CumulusCore\Traits\CumulusTestHelpers;

    /**
     * @test *
     * @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function admin_can_add_user_to_cluster($userData, $clusterData)
    {
        $this->signInToBackend()
            ->createUser($userData)
            ->createCluster($clusterData)
            ->activateUser($userData['email'])
            ->addUserToCluster($userData['email'], $clusterData['name'])
            ->see('User updated');
    }

    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }

}
