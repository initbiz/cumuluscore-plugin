<?php

use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class AddUserToCompanyTest extends Ui2TestCase {

    use CumulusDataProviders,
        CumulusHelpers;
    /**
     * @test *
     * @dataProvider providerUserWithCompanyData
     * * @return void
     */
    public function admin_can_add_user_to_company($userData, $companyData)
    {
        $this->signInToBackend()
            ->createUser($userData)
            ->createCompany($companyData)
            ->activateUser($userData['email'])
            ->addUserToCompany($userData['email'], $companyData['name'])
            ->see('User updated');
    }

}