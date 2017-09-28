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
             ->clickLabel('Send invitation by email')
             ->press('Create')
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
             ->findAndClickElement("Login button", "//button[@type='submit']")
             ->hold(2);
        return $this;
    }

    public function addUserToCompany($userEmail, $company)
    {
        $userId = $this->getRecordID($userEmail, '/panel/rainlab/user/users');
        $this->visit('/panel/rainlab/user/users/update/' . $userId)
             ->findAndClickElement('companies', '//a[@title="Companies"]')
             ->hold(2)
             ->clickLabel($company)
             ->hold(2)
             ->press('Save')
             ->waitForFlashMessage();
        return $this;
    }

    public function addModuleToCompany($module, $company)
    {
        $companyId = $this->getRecordID($company, 'panel/initbiz/cumuluscore/companies');
        $this->visit('/panel/initbiz/cumuluscore/companies/update/' . $companyId)
             ->clickLabel($module);
        $this->hold(1)
             ->press('Save')
             ->waitForFlashMessage();
        return $this;
    }

    public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

}