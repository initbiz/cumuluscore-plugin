<?php

use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class CreateCompanyTest extends Ui2TestCase {

    use CumulusDataProviders,
        OctoberSeleniumHelpers,
        CumulusHelpers;

    /**
     * @test *
     * @dataProvider providerCompanyData
     * * @return void
     */
    public function admin_can_create_company($data)
    {
        $this->signInToBackend()
             ->createCompany($data);
        $this->see('Companies created');
    }
}