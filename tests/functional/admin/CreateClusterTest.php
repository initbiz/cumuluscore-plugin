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


    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }
}