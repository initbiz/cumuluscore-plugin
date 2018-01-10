<?php
use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class AttachPlanToClusterTest extends Ui2TestCase {

    use CumulusHelpers,
        CumulusDataProviders;
    /**
     * @test *
     * @dataProvider providerClusterData
     * * @return void
     */
    public function admin_can_attach_plan_to_cluster($clusterData)
    {
        $this->signInToBackend()
             ->createCluster($clusterData)
             ->createPlan('Example Plan')
             ->attachClusterToPlan('Example Plan', $clusterData['name'])
             ->see('Clusters updated');
    }
    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }
}