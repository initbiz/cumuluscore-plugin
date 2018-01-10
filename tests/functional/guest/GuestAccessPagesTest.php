<?php

use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class GuestAccessPagesTest extends Ui2TestCase {

    use CumulusHelpers,
        CumulusDataProviders;
    /**
     * @test *
     * * @return void
     */
    public function guest_cannot_enter_choose_cluster_page()
    {
        $this->visit('/system/choose-cluster')
        ->see('Forbidden');
        //sign in to backed for clearCumulus
        $this->signInToBackend();
    }

    /**
     * @test *
     * @dataProvider providerClusterData
     * * @return void
     */
    public function guest_cannot_enter_cluster_dashboard_page($clusterData)
    {
        $clusterSlug = $this->slugify($clusterData['name']);
        $this->signInToBackend()
             ->createCluster($clusterData)
             ->visit('panel/backend/auth/signout')
             ->visit('/system/'. $clusterSlug .'/dashboard')
             ->see('Forbidden');
        //sign in to backed for clearCumulus
        $this->signInToBackend();
    }

    /**
     * @test *
     * @dataProvider providerClusterData
     * * @return void
     */
    public function guest_cannot_enter_module_guarded_page($clusterData)
    {
        $clusterSlug = $this->slugify($clusterData['name']);
        $this->signInToBackend()
             ->createCluster($clusterData)
             ->createPlan('Example plan')
             ->addModuleToPlan('CumulusProducts', 'Example plan')
             ->attachClusterToPlan('Example plan', $clusterData['name'])
             ->hold(2)
             ->visit('panel/backend/auth/signout')
             ->hold(2)
             ->visit('/system/'. $clusterSlug .'/products')
             ->see('Forbidden');
        //sign in to backed for clearCumulus
        $this->signInToBackend();
    }


    protected function afterTest()
    {
        $this->hold(2)
             ->clearCumulus();
    }

}