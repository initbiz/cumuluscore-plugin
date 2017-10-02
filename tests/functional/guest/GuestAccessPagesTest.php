<?php

use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class GuestAccessPagesTest extends Ui2TestCase {

    use CumulusHelpers,
        CumulusDataProviders;
    /**
     * @test *
     * * @return void
     */
    public function guest_cannot_enter_choose_company_page()
    {
        $this->visit('/system/choose-company')
        ->see('Forbidden');
        //sign in to backed for clearCumulus
        $this->signInToBackend();
    }

    /**
     * @test *
     * @dataProvider providerCompanyData
     * * @return void
     */
    public function guest_cannot_enter_company_dashboard_page($companyData)
    {
        $companySlug = $this->slugify($companyData['name']);
        $this->signInToBackend()
             ->createCompany($companyData)
             ->visit('panel/backend/auth/signout')
             ->visit('/system/'. $companySlug .'/dashboard')
             ->see('Forbidden');
        //sign in to backed for clearCumulus
        $this->signInToBackend();
    }

    /**
     * @test *
     * @dataProvider providerCompanyData
     * * @return void
     */
    public function guest_cannot_enter_module_guarded_page($companyData)
    {
        $companySlug = $this->slugify($companyData['name']);
        $this->signInToBackend()
             ->createCompany($companyData)
             ->hold(2)
             ->visit('panel/backend/auth/signout')
             ->hold(2)
             ->visit('/system/'. $companySlug .'/products')
             ->see('Forbidden');
        //sign in to backed for clearCumulus
        $this->signInToBackend();
    }


    protected function afterTest()
    {
        $this->hold(2)
             ->clearCumulus();
    }

}