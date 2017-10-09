<?php

use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class EnterCompanyDashboardPageTest extends Ui2TestCase
{

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
            ->signInToFrontend($userData)
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
            ->hold(1)
            ->signInToFrontend($user)
            ->seePageIs('/system/choose-company')
            ->findAndClickElement($firstCompany['name'], "//h2[contains(., '{$firstCompany['name']}')]")
            ->hold(1)
            ->see('Dashboard')
            ->seePageIs('/system/' . $this->slugify($firstCompany['name']) . '/dashboard')
            ->visit('/system/choose-company')
            ->findAndClickElement($secondCompany['name'], "//h2[contains(., '{$secondCompany['name']}')]")
            ->hold(1)
            ->see('Dashboard')
            ->seePageIs('/system/' . $this->slugify($secondCompany['name']) . '/dashboard');
    }


    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }
}