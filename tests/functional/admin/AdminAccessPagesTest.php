<?php
use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class AdminAccessPagesTest extends Ui2TestCase {

    use CumulusDataProviders,
        CumulusHelpers;
    /**
     * @test *
     * * @return void
     */
    public function admin_cannot_enter_choose_company_page()
    {
            $this->signInToFrontend([
                'email' => TEST_SELENIUM_USER,
                'password' => TEST_SELENIUM_PASS,
            ])
            ->visit('/system/choose-company')
            ->hold(1)
            ->see('Forbidden');
        //sign in to backed for clearCumulus
        $this->signInToBackend();
    }

    /**
     * @test *
     * @dataProvider providerCompanyData
     * * @return void
     */
    public function admin_cannot_enter_company_dashboard_page($companyData)
    {
        $companySlug = $this->slugify($companyData['name']);
        $this->signInToBackend()
            ->createCompany($companyData)
            ->signInToFrontend([
                'email' => TEST_SELENIUM_USER,
                'password' => TEST_SELENIUM_PASS,
            ])
            ->visit('/system/' . $companySlug . '/dashboard')
            ->hold(1)
            ->see('Forbidden');
    }

    /**
     * @test *
     * @dataProvider providerCompanyData
     * * @return void
     */
    public function admin_cannot_enter_module_guarded_page($companyData)
    {
        $companySlug = $this->slugify($companyData['name']);
        $this->signInToBackend()
            ->createCompany($companyData)
            ->addModuleToCompany('CumulusProducts', $companyData['name'])
            ->signInToFrontend([
                'email' => TEST_SELENIUM_USER,
                'password' => TEST_SELENIUM_PASS,
            ])
            ->visit('/system/' . $companySlug . '/products')
            ->hold(1)
            ->see('Forbidden');
    }

    /**
     * @test *
     * * @return void
     */
    public function admin_cannot_sign_in_to_frontend()
    {
        $this->signInToFrontend([
                'email' => TEST_SELENIUM_USER,
                'password' => TEST_SELENIUM_PASS,
             ])
             ->see('Something bad happened, mate, here it is');
        //sign in to backed for clearCumulus
        $this->signInToBackend();
    }

    protected function afterTest()
    {
        $this->clearCumulus();
    }
}