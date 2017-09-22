<?php

trait CumulusHelpers {

    public function createUser($data)
    {
        $this->visit('/panel/rainlab/user/users/create')
             ->type($data['name'], 'Form-field-User-name')
             ->type($data['surname'], 'Form-field-User-surname')
             ->type($data['email'], 'Form-field-User-email')
             ->type($data['password'], 'Form-field-User-password')
             ->type($data['password'], 'Form-field-User-password_confirmation')
             ->press('Create')
             ->waitForElementsWithClass('flash-message');
        return $this;
    }

    public function createCompany($data)
    {
        $this->visit('/panel/initbiz/cumuluscore/companies/create')
             ->type($data['company'], 'Form-field-Company-full_name')
             ->press('Create')
             ->waitForElementsWithClass('flash-message');
        return $this;
    }

    public function activateUser($email)
    {
//        $this->visit('/panel/rainlab/user/users')
//        $this->hold(1)
//            ->findElement('user', '/html/body/div[1]/div/div[2]/div/div[2]/div/div/div/div[3]/div/table/tbody/tr[1]')
//            ->click();
//        $this->findElement('active user', '/html/body/div[1]/div/div[2]/div/div[2]/div/div/div/form/div/div[1]/div/div[2]/div/div[1]/div/div/p/a')
//            ->click();
//        $this->waitForElementsWithClass('sweet-alert')
//            ->hold(3)
//            ->findElement('ok button', '/html/body/div[5]/div[2]/p[2]/button[2]')
//            ->click();
//        $this->hold(3)
//            ->see('User has been activated');
    }

    public function createUserWithCompany($userData, $companyData)
    {
        $this->visit('/panel/rainlab/user/users/create')
            ->type($data['name'], 'Form-field-User-name')
            ->type($data['surname'], 'Form-field-User-surname')
            ->type($data['email'], 'Form-field-User-email')
            ->type($data['password'], 'Form-field-User-password')
            ->type($data['password'], 'Form-field-User-password_confirmation')
            ->press('Create')
            ->waitForElementsWithClass('flash-message');

        return $this;
    }
}