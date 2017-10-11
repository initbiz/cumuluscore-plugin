<?php
use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class InactiveUserAccessPagesTest extends Ui2TestCase {

    use CumulusDataProviders,
        CumulusHelpers;
    /**
     * @test *
     *  @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function inactive_user_cannot_enter_choose_cluster_page($userData, $clusterData)
    {
        $clusterSlug = $this->slugify($clusterData['name']);
        $this->signInToBackend()
            ->createUser($userData)
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
    public function inactive_user_cannot_enter_cluster_dashboard_page($userData, $clusterData)
    {
        $clusterSlug = $this->slugify($clusterData['name']);
        $this->signInToBackend()
             ->createUser($userData)
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
    public function inactive_user_cannot_enter_module_guarded_page($userData, $clusterData)
    {
        $clusterSlug = $this->slugify($clusterData['name']);
        $this->signInToBackend()
            ->createUser($userData)
            ->createCluster($clusterData)
            ->createPlan('Example plan')
            ->addModuleToPlan('CumulusProducts', 'Example plan')
            ->addPlanToCluster('Example plan', $clusterData['name'])
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
    public function inactive_user_cannot_enter_backend($userData)
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