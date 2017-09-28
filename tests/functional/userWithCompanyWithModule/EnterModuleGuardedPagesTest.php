<?php

use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class EnterModuleGuardedPagesTest extends Ui2TestCase {

    use CumulusDataProviders,
        CumulusHelpers;
    /**
     * @test *
     * @dataProvider providerUserWithCompanyData
     * * @return void
     */
    public function user_with_company_with_module_can_visit_module_guarded_page($userData, $companyData)
    {
        $this->signInToBackend()
            ->createUser($userData)
            ->createCompany($companyData)
            ->activateUser($userData['email'])
            ->addUserToCompany($userData['email'], $companyData['name'])
            ->hold(2)
            ->addModuleToCompany('CumulusProducts', $companyData['name'])
            ->singInToFrontend($userData)
            ->clickLink('Products')
            ->see('List Products');
    }

    /**
     * @test *
     * @dataProvider providerUserWithCompanyData
     * * @return void
     */
    public function user_with_company_without_module_cannot_visit_module_guarded_page($userData, $companyData)
    {
        $this->signInToBackend()
            ->createUser($userData)
            ->createCompany($companyData)
            ->activateUser($userData['email'])
            ->addUserToCompany($userData['email'], $companyData['name'])
            ->hold(2)
            ->singInToFrontend($userData)
            ->notSee('Products');
    }
}


//user z jedna firma nie moze wejsc do drugiej
//dwa moduły, do jegodnego ma wjazd do drugiego