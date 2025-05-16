<?php

declare(strict_types=1);
use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class AdminAccessPagesTest extends Ui2TestCase
{
    use Initbiz\CumulusCore\Traits\CumulusDataProviders;
    use Initbiz\CumulusCore\Traits\CumulusTestHelpers;

    /**
     * @test *
     * * @return void
     */
    public function admin_cannot_enter_choose_cluster_page()
    {
        $this->signInToFrontend([
            'email' => TEST_SELENIUM_USER,
            'password' => TEST_SELENIUM_PASS,
        ])
        ->visit('/system/choose-cluster')
        ->hold(1)
        ->see('Forbidden');
        //sign in to backend for clearCumulus
        $this->signInToBackend();
    }

    /**
     * @test *
     * @dataProvider providerClusterData
     * * @return void
     */
    public function admin_cannot_enter_cluster_dashboard_page($clusterData)
    {
        $clusterSlug = $this->slugify($clusterData['name']);
        $this->signInToBackend()
            ->createCluster($clusterData)
            ->signInToFrontend([
                'email' => TEST_SELENIUM_USER,
                'password' => TEST_SELENIUM_PASS,
            ])
            ->visit('/system/' . $clusterSlug . '/dashboard')
            ->hold(1)
            ->see('Forbidden');
    }

    /**
     * @test *
     * @dataProvider providerClusterData
     * * @return void
     */
    public function admin_cannot_enter_module_guarded_page($clusterData)
    {
        $clusterSlug = $this->slugify($clusterData['name']);
        $this->signInToBackend()
            ->createCluster($clusterData)
            ->createPlan('Example plan')
            ->addModuleToPlan('CumulusProducts', 'Example plan')
            ->attachClusterToPlan('Example plan', $clusterData['name'])
            ->signInToFrontend([
                'email' => TEST_SELENIUM_USER,
                'password' => TEST_SELENIUM_PASS,
            ])
            ->visit('/system/' . $clusterSlug . '/products')
            ->hold(1)
            ->see('Forbidden');
    }

    /**
     * @test *
     * * @return void
     */
    public function admin_cannot_sign_in_to_frontend()
    {
        $this->signInToFrontend([
                'email' => TEST_SELENIUM_USER,
                'password' => TEST_SELENIUM_PASS,
             ])
             ->see('Something bad happened, mate, here it is');
        //sign in to backed for clearCumulus
        $this->signInToBackend();
    }

    protected function afterTest()
    {
        $this->clearCumulus();
    }
}
