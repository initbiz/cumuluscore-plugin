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
     * @dataProvider providerUserWithCompanyData
     * * @return void
     */
    public function user_with_company_with_module_cannot_enter_another_module_page($userData, $FirstCompanyData)
    {
        $secondCompanyData = $this->fakeCompanyData();
        $secondCompanySlug = $this->slugify($secondCompanyData['name']);
        $this->signInToBackend()
            ->createUser($userData)
            ->createCompany($FirstCompanyData)
            ->createCompany($secondCompanyData)
            ->activateUser($userData['email'])
            ->addUserToCompany($userData['email'], $FirstCompanyData['name'])
            ->hold(1)
            ->addModuleToCompany('CumulusProducts', $FirstCompanyData['name'])
            ->hold(1)
            ->addModuleToCompany('CumulusElearning', $secondCompanyData['name'])
            ->hold(2)
            ->signInToFrontend($userData)
            ->visit('system/' . $secondCompanySlug . '/products')
            ->hold(2)
            ->see('Forbidden');
    }

    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }

}