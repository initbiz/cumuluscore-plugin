<?php

use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class MenuTest extends Ui2TestCase {

    use CumulusDataProviders,
        CumulusHelpers;
    /**
     * @test *
     * @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function user_with_two_modules_can_enter_both_module_guarded_pages_by_menu($userData, $clusterData)
    {
        $this->signInToBackend()
             ->createUser($userData)
             ->activateUser($userData['email'])
             ->createCluster($clusterData)
             ->createPlan('Example plan')
             ->addUserToCluster($userData['email'], $clusterData['name'])
             ->addModuleToPlan('CumulusProducts', 'Example plan')
             ->addModuleToPlan('CumulusElearning', 'Example plan')
             ->addPlanToCluster('Example plan', $clusterData['name'])
             ->signInToFrontend($userData)
             ->hold(5)
             ->clickLink('Products')
             ->see('List Products')
             ->visit('/system/' . $this->slugify($clusterData['name']) . '/dashboard')
             ->clickLink('E-Learning')
             ->see('E-Learning');

    }

    /**
     * @test *
     * @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function user_with_one_module_cannot_enter_another_module_page_by_menu($userData, $clusterData)
    {
        $this->signInToBackend()
            ->createUser($userData)
            ->activateUser($userData['email'])
            ->createCluster($clusterData)
            ->createPlan('Example plan')
            ->addUserToCluster($userData['email'], $clusterData['name'])
            ->addModuleToPlan('CumulusProducts', 'Example plan')
            ->addPlanToCluster('Example plan', $clusterData['name'])
            ->signInToFrontend($userData)
            ->see('Products')
            ->notSee('E-Learning');

    }

    /**
     * @test *
     * @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function user_without_module_cannot_enter_module_pages_by_menu($userData, $clusterData)
    {
        $this->signInToBackend()
            ->createUser($userData)
            ->activateUser($userData['email'])
            ->createCluster($clusterData)
            ->addUserToCluster($userData['email'], $clusterData['name'])
            ->signInToFrontend($userData)
            ->notSee('Products');
            $this->notSee('E-Learning');
    }

    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }

}