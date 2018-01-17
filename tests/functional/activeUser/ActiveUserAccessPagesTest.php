<?php

use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class ActiveUserAccessPagesTest extends Ui2TestCase {

    use Initbiz\CumulusCore\Traits\CumulusTestHelpers;
    use Initbiz\CumulusCore\Traits\CumulusDataProviders;
    use Initbiz\Selenium2tests\Traits\SeleniumHelpers;

    /**
     * @test *
     * @dataProvider providerUserData
     * * @return void
     */
    public function active_user_can_enter_choose_cluster_page($userData)
    {
        $this->signInToBackend()
             ->createUser($userData)
             ->activateUser($userData['email'])
             ->signInToFrontend($userData)
             ->visit('/system/choose-cluster')
             ->notSee('Forbidden');
    }

    /**
     * @test *
     * @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function active_user_cannot_enter_cluster_dashboard_page($userData, $clusterData)
    {
        $clusterSlug = $this->slugify($clusterData['name']);
        $this->signInToBackend()
            ->createUser($userData)
            ->activateUser($userData['email'])
            ->createCluster($clusterData)
            ->signInToFrontend($userData)
            ->visit('/system/' . $clusterSlug . '/dashboard')
            ->hold(1)
            ->see('Forbidden');
    }

    /**
     * @test *
     * @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function active_user_cannot_enter_module_guarded_pages($userData, $clusterData)
    {
        $clusterSlug = $this->slugify($clusterData['name']);
        $this->signInToBackend()
            ->createUser($userData)
            ->activateUser($userData['email'])
            ->createCluster($clusterData)
            ->createPlan('Example Plan Products')
            ->addModuleToPlan('CumulusProducts', 'Example Plan Products')
            ->attachClusterToPlan('Example Plan Products', $clusterData['name'])
            ->signInToFrontend($userData)
            ->visit('/system/' . $clusterSlug . '/products')
            ->hold(1)
            ->see('Forbidden');
    }

    /**
     * @test *
     * @dataProvider providerUserData
     * * @return void
     */
    public function active_user_cannot_enter_to_backend($userData)
    {
        $this->signInToBackend()
             ->createUser($userData)
             ->visit('panel/backend/auth/signout')
             ->visit('/panel')
             ->type($userData['email'], 'login')
             ->type($userData['password'], 'password')
             ->findAndClickElement("Login button", "//button[@type='submit']")
             ->waitForFlashMessage()
             ->hold(2)
             ->see('A user was not found with the given credentials');
        //sign in to backed for clearCumulus
        $this->signInToBackend();
    }

    protected function afterTest()
    {
        $this->clearCumulus();
    }
}
