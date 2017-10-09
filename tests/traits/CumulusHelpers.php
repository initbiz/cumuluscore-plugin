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

    public function createPlan($name)
    {
        $this->visit('/panel/initbiz/cumuluscore/plans/create')
             ->type($name, 'Form-field-Plan-name')
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


    public function signInToFrontend($data)
    {
        $this->visit('/')
             ->type($data['email'], 'login')
             ->type($data['password'], 'password')
             ->findAndClickElement("Login button", "//button[@type='submit']")
             ->hold(2);
        return $this;
    }

    public function addUserToCompany($userEmail, $companyName)
    {
        $userId = $this->getRecordID($userEmail, '/panel/rainlab/user/users');
        $this->visit('/panel/rainlab/user/users/update/' . $userId)
             ->findAndClickElement('companies', '//a[@title="Companies"]')
             ->hold(2)
             ->clickLabel($companyName)
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
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }

    public function clearCumulus()
    {
        $this->deleteAllUsers()
             ->deleteAllCompany();
        return $this;
    }

    public function deleteAllUsers()
    {
        $this->visit('/panel/rainlab/user/users')
             ->hold(2)
             ->typeInBackendSearch('', true)
             ->hold(1)
             ->findAndClickElement('check all', "/html/body/div[1]/div/div[2]/div/div[2]/div/div/div/div[3]/div/table/thead/tr/th[1]")
             ->press('Delete selected')
             ->waitForElementsWithClass('sweet-alert')
             ->hold(1)
             ->press('OK')
             ->waitForFlashMessage()
             ->hold(2);
        return $this;
    }

    public function deleteAllCompany()
    {
        $this->visit('/panel/initbiz/cumuluscore/companies')
             ->hold(2)
             ->findAndClickElement('check all company', "/html/body/div[1]/div/div[2]/div/div[2]/div/div/div/div[2]/div/table/thead/tr/th[1]")
             ->press('Delete selected')
             ->waitForElementsWithClass('sweet-alert')
             ->hold(2)
             ->press('OK')
             ->waitForFlashMessage()
             ->hold(2);
        return $this;
    }


}