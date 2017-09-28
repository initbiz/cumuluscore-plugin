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
             ->signOutFromBackend()
             ->visit('/system/'. $companySlug .'/dashboard')
             ->see('Forbidden');
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
             ->signOutFromBackend()
             ->hold(2)
             ->visit('/system/'. $companySlug .'/products')
             ->see('Forbidden');
    }


    protected function afterTest()
    {
        $this->hold(2)
             ->signInToBackend()
             ->clearCumulus();
    }

}