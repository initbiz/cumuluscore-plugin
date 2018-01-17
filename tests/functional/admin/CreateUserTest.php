<?php

use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class CreateUserTest extends Ui2TestCase {

    use Initbiz\Selenium2tests\Traits\OctoberSeleniumHelpers;
    use Initbiz\CumulusCore\Traits\CumulusDataProviders;
    use Initbiz\CumulusCore\Traits\CumulusTestHelpers;

    /**
     * @test *
     * @dataProvider providerUserData
     * * @return void
     */
    public function admin_can_create_user($data)
    {
        $this->signInToBackend()
            ->createUser($data)
            ->hold(1)
            ->see('User created');
    }

    /**
     * @test *
     * @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function admin_can_create_user_with_one_cluster($userData, $clusterData)
    {
        //still not green
        $this->signInToBackend()
             ->createCluster($clusterData)
             ->createUser($userData)
             ->addUserToCluster($userData['email'], $clusterData['name'])
             ->hold(1)
             ->see('User updated');
    }

    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }
}
