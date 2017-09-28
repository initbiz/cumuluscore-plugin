<?php
use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class AddModuleToCompanyTest extends Ui2TestCase {

    use CumulusDataProviders,
        CumulusHelpers;
    /**
     * @test *
     * @dataProvider providerUserWithCompanyData
     * * @return void
     */
    public function admin_can_add_module_to_company($userData, $companyData)
    {
        $this->signInToBackend()
            ->createUser($userData)
            ->createCompany($companyData)
            ->activateUser($userData['email'])
            ->addUserToCompany($userData['email'], $companyData['name'])
            ->hold(2)
            ->addModuleToCompany('CumulusProducts', $companyData['name'])
            ->see('Companies updated');
    }
}