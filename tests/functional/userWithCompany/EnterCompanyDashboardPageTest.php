<?php

use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class EnterCompanyDashboardPageTest extends Ui2TestCase {

    use CumulusDataProviders,
        CumulusHelpers;
    /**
     * @test *
     * @dataProvider providerUserWithCompanyData
     * * @return void
     */
    public function user_with_company_can_enter_companies_dashboard($userData, $companyData)
    {
        $this->signInToBackend()
             ->createUser($userData)
             ->createCompany($companyData)
             ->activateUser($userData['email'])
             ->addUserToCompany($userData['email'], $companyData['name'])
             ->hold(2)
             ->singInToFrontend($userData)
             ->hold(2)
             ->see('Dashboard');
    }

    /**
     * @test *
     * * @return void
     */
    public function user_with_two_companies_can_enter_both_companies_dashboard()
    {
        $user = $this->fakeUserData();
        $firstCompany = $this->fakeCompanyData();
        $secondCompany = $this->fakeCompanyData();
        $this->signInToBackend()
             ->createUser($user)
             ->createCompany($firstCompany)
             ->createCompany($secondCompany)
             ->activateUser($user['email'])
             ->addUserToCompany($user['email'], $firstCompany['name'])
             ->hold(2)
             ->addUserToCompany($user['email'], $secondCompany['name'])
             ->hold(2)
             ->singInToFrontend($user)
             ->seePageIs('/system/choose-company');
             $this->findElement($firstCompany['name'], "//h2[contains(., '{$firstCompany['name']}')]")
             ->click();
             $this->hold(1)
                  ->see('Dashboard');
             $this->visit('/system/choose-company')
                  ->findElement($secondCompany['name'], "//h2[contains(., '{$secondCompany['name']}')]")
             ->click();
             $this->hold(1)
                  ->see('Dashboard');
    }

}