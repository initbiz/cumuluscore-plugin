<?php

use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class UserWithCompanyWithModuleAccessPagesTest extends Ui2TestCase {

    use CumulusHelpers,
        CumulusDataProviders;
    /**
     * @test *
     * @dataProvider providerUserWithCompanyData
     * * @return void
     */
    public function user_with_company_without_module_cannot_enter_module($userData, $companyData)
    {
        $companySlug = $this->slugify($companyData['name']);
        $this->signInToBackend()
            ->createUser($userData)
            ->createCompany($companyData)
            ->activateUser($userData['email'])
            ->addUserToCompany($userData['email'], $companyData['name'])
            ->hold(2)
            ->signInToFrontend($userData)
            ->visit('system/' . $companySlug . '/products')
            ->hold(2)
            ->see('Forbidden');
    }

    /**
     * @test *
     * @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function user_with_cluster_with_module_cannot_enter_another_module_page($userData, $FirstClusterData)
    {
        $secondClusterData = $this->fakeClusterData();
        $secondClusterSlug = $this->slugify($secondClusterData['name']);
        $this->signInToBackend()
            ->createUser($userData)
            ->createCluster($FirstClusterData)
            ->createCluster($secondClusterData)
            ->activateUser($userData['email'])
            ->addUserToCluster($userData['email'], $FirstClusterData['name'])
            ->hold(1)
            ->addModuleToCluster('CumulusProducts', $FirstClusterData['name'])
            ->hold(1)
            ->addModuleToCluster('CumulusElearning', $secondClusterData['name'])
            ->hold(2)
            ->signInToFrontend($userData)
            ->visit('system/' . $secondClusterSlug . '/products')
            ->hold(2)
            ->see('Forbidden');
    }

    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }

}