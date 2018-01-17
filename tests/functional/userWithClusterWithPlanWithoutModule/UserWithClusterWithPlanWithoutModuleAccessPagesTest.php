<?php

use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class UserWithClusterWithPlanWithoutModuleAccessPagesTest extends Ui2TestCase {

    use Initbiz\CumulusCore\Traits\CumulusTestHelpers;
    use Initbiz\CumulusCore\Traits\CumulusDataProviders;

    /**
     * @test *
     * @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function user_with_cluster_with_plan_without_module_cannot_visit_module_guarded_page($userData, $clusterData)
    {
        $this->signInToBackend()
            ->createUser($userData)
            ->createCluster($clusterData)
            ->activateUser($userData['email'])
            ->addUserToCluster($userData['email'], $clusterData['name'])
            ->createPlan('Example plan')
            ->attachClusterToPlan('Example plan', $clusterData['name'])
            ->hold(2)
            ->signInToFrontend($userData)
            ->visit('system/' . $this->slugify($clusterData['name']) . '/products')
            ->notSee('List Products');
    }

    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }

}
