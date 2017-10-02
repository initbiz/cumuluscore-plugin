<?php

use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class UserWithCompanyAccessPagesTest extends Ui2TestCase {
    
    use CumulusDataProviders,
        CumulusHelpers;

    /**
     * @test *
     * * @return void
     */
    public function user_with_company_cannot_enter_another_company_dashboard_page()
    {
        $firstCompanyData = $this->fakeCompanyData();
        $secondCompanyData = $this->fakeCompanyData();
        $userData = $this->fakeUserData();
        $secondCompanySlug = $this->slugify($secondCompanyData['name']);
        $this->signInToBackend()
             ->createUser($userData)
             ->activateUser($userData['email'])
             ->createCompany($firstCompanyData)
             ->createCompany($secondCompanyData)
             ->addUserToCompany($userData['email'], $firstCompanyData['name'])
             ->hold(1)
             ->signInToFrontend($userData)
             ->visit('/system/' .$secondCompanySlug.'/dashboard')
             ->hold(2)
             ->see('Forbidden');
    }

    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }
}