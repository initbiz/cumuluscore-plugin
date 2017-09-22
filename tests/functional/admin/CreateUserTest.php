<?php

use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class CreateUserTest extends Ui2TestCase {

    use CumulusDataProviders,
        CumulusHelpers,
        OctoberSeleniumHelpers;
        
    /**
     * @test *
     * @dataProvider providerUserData
     * * @return void
     */
    public function admin_can_create_user($data)
    {
        $this->signInToBackend()
            ->createUser($data)
            ->hold(1)
            ->see('User created');
    }

    /**
     * @test *
     * @dataProvider providerUserWithCompanyData
     * * @return void
     */
    public function admin_can_create_user_with_one_company($userData, $companyData)
    {
//        $this->signInToBackend()
//             ->createCompany($companyData)
//             ->createUserWithCompany($userData, $companyData)
//             ->hold(1);

    }
}