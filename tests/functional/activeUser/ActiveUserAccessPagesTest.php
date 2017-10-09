<?php

use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class ActiveUserAccessPagesTest extends Ui2TestCase {

    use CumulusDataProviders,
        CumulusHelpers;
    /**
     * @test *
     * @dataProvider providerUserData
     * * @return void
     */
    public function active_user_can_enter_choose_company_page($userData)
    {
        $this->signInToBackend()
             ->createUser($userData)
             ->activateUser($userData['email'])
             ->signInToFrontend($userData)
             ->visit('/system/choose-company')
             ->notSee('Forbidden');
    }

    /**
     * @test *
     * @dataProvider providerUserWithCompanyData
     * * @return void
     */
    public function active_user_cannot_enter_company_dashboard_page($userData, $companyData)
    {
        $companySlug = $this->slugify($companyData['name']);
        $this->signInToBackend()
            ->createUser($userData)
            ->activateUser($userData['email'])
            ->createCompany($companyData)
            ->signInToFrontend($userData)
            ->visit('/system/' . $companySlug . '/dashboard')
            ->hold(1)
            ->see('Forbidden');
    }

    /**
     * @test *
     * @dataProvider providerUserWithCompanyData
     * * @return void
     */
    public function active_user_cannot_enter_module_guarded_pages($userData, $companyData)
    {
        $companySlug = $this->slugify($companyData['name']);
        $this->signInToBackend()
            ->createUser($userData)
            ->activateUser($userData['email'])
            ->createCompany($companyData)
            ->addModuleToCompany('CumulusProducts', $companyData['name'])
            ->signInToFrontend($userData)
            ->visit('/system/' . $companySlug . '/products')
            ->hold(1)
            ->see('Forbidden');
    }

    /**
     * @test *
     * @dataProvider providerUserData
     * * @return void
     */
    public function active_user_cannot_enter_to_backend($userData)
    {
        $this->signInToBackend()
             ->createUser($userData)
             ->visit('panel/backend/auth/signout')
             ->visit('/panel')
             ->type($userData['email'], 'login')
             ->type($userData['password'], 'password')
             ->findAndClickElement("Login button", "//button[@type='submit']")
             ->waitForFlashMessage()
             ->hold(2)
             ->see('A user was not found with the given credentials');
        //sign in to backed for clearCumulus
        $this->signInToBackend();
    }

    protected function afterTest()
    {
        $this->clearCumulus();
    }
}