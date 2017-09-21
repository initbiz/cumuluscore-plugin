<?php

trait CumulusHelpers {

    public function createUser($data)
    {
        $this->signInToBackend()
             ->visit('/panel/rainlab/user/users/create')
             ->type($data['name'], 'Form-field-User-name')
             ->type($data['surname'], 'Form-field-User-surname')
             ->type($data['email'], 'Form-field-User-email')
             ->type($data['password'], 'Form-field-User-password')
             ->type($data['password'], 'Form-field-User-password_confirmation')
             ->press('Create');
        return $this;
    }

    public function createCompany($data)
    {
        $this->signInToBackend()
             ->visit('/panel/initbiz/cumuluscore/companies/create')
             ->type($data['company'], 'Form-field-Company-full_name')
             ->press('Create');
        return $this;
    }
}