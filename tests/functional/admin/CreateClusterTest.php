<?php

use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class CreateClusterTest extends Ui2TestCase {

    use CumulusDataProviders,
        CumulusHelpers;

    /**
     * @test *
     * @dataProvider providerClusterData
     * * @return void
     */
    public function admin_can_create_cluster($data)
    {
        $this->signInToBackend()
             ->createCluster($data);
        $this->see('Clusters created');
    }

    /**
     * @test *\
     * @dataProvider providerClusterData
     * * @return void
     */
    public function admin_can_create_cluster_with_plan($data)
    {
        $this->signInToBackend()
            ->createCluster($data)
            ->createPlan('Example plan')
            ->addPlanToCluster($data['name'], 'Example plan')
            ->see('Clusters updated');
    }

    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }
}