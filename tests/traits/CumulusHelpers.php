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
             ->findElement("email Checkbox", "//label[contains(., 'Send invitation by email')]")
             ->click();
             $this->press('Create')
             ->waitForFlashMessage();
        return $this;
    }

    public function createCompany($data)
    {
        $this->visit('/panel/initbiz/cumuluscore/companies/create')
             ->type($data['name'], 'Form-field-Company-full_name')
             ->press('Create')
             ->waitForFlashMessage();
        return $this;
    }

    public function activateUser($email)
    {
        $this->visit('/panel/rainlab/user/users')
             ->clickRowInBackendList($email)
             ->clickLink('Activate this user manually')
             ->waitForElementsWithClass('sweet-alert')
             ->hold(1)
             ->press('OK')
             ->waitForFlashMessage();
        return $this;
    }


    public function singInToFrontend($data)
    {
        $this->visit('/')
             ->type($data['email'], 'login')
             ->type($data['password'], 'password')
             ->findElement("Login button", "//button[@type='submit']")
             ->click();
        $this->hold(2);
        return $this;
    }

    public function addUserToCompany($userEmail, $company)
    {
        $userId = $this->getRecordID($userEmail, '/panel/rainlab/user/users');
        $this->visit('/panel/rainlab/user/users/preview/' . $userId)
             ->clickLink('Update details')
             ->hold(1)
             ->findElement('companies', '//a[@title="Companies"]')
             ->click();
        $this->hold(2);
        $this->findElement($company, "//label[contains(., '{$company}')]")
             ->click();
        $this->hold(2)
             ->press('Save')
             ->waitForFlashMessage();
        return $this;
    }

    public function addModuleToCompany($module, $company)
    {
        $companyId = $this->getRecordID($company, 'panel/initbiz/cumuluscore/companies');
        $this->visit('/panel/initbiz/cumuluscore/companies/update/' . $companyId)
             ->findElement($module, "//label[contains(., '{$module}')]")
             ->click();
        $this->hold(1)
             ->press('Save')
             ->waitForFlashMessage();
        return $this;
    }


}