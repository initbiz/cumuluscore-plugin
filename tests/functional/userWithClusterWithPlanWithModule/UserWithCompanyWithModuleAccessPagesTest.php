<?php

use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class UserWithClusterWithPlanWithModuleAccessPagesTest extends Ui2TestCase {

    use CumulusHelpers,
        CumulusDataProviders;

    /**
     * @test *
     * @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function user_with_cluster_with_plan_with_module_cannot_enter_another_module_page($userData, $FirstClusterData)
    {
        $secondClusterData = $this->fakeClusterData();
        $secondClusterSlug = $this->slugify($secondClusterData['name']);
        $this->signInToBackend()
            ->createUser($userData)
            ->createCluster($FirstClusterData)
            ->createCluster($secondClusterData)
            ->activateUser($userData['email'])
            ->createPlan('Example plan products')
            ->createPlan('Example plan elearning')
            ->addUserToCluster($userData['email'], $FirstClusterData['name'])
            ->hold(1)
            ->addModuleToPlan('CumulusProducts', 'Example plan products')
            ->hold(1)
            ->addModuleToPlan('CumulusElearning', 'Example plan elearning')
            ->hold(2)
            ->addPlanToCluster('Example plan products', $FirstClusterData['name'])
            ->addPlanToCluster('Example plan elearning', $secondClusterData['name'])
            ->signInToFrontend($userData)
            ->visit('system/' . $secondClusterSlug . '/elearning')
            ->hold(2)
            ->see('Forbidden');
    }

    /**
     * @test *
     * @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function user_with_cluster_with_plan_with_module_can_visit_module_guarded_page($userData, $clusterData)
    {
        $this->signInToBackend()
            ->createUser($userData)
            ->createCluster($clusterData)
            ->activateUser($userData['email'])
            ->addUserToCluster($userData['email'], $clusterData['name'])
            ->createPlan('Example plan')
            ->addModuleToPlan('CumulusProducts', 'Example plan')
            ->addPlanToCluster('Example plan', $clusterData['name'])
            ->signInToFrontend($userData)
            ->visit('system/' . $this->slugify($clusterData['name']) . '/products')
            ->see('List Products');
    }

    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }

}