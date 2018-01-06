<?php

trait CumulusHelpers {


    public function createUser($data)
    {
        $this->visit(TEST_SELENIUM_BACKEND_URL. '/rainlab/user/users/create')
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

    public function createCluster($data)
    {
        $this->visit(TEST_SELENIUM_BACKEND_URL.'/initbiz/cumuluscore/clusters/create')
             ->type($data['name'], 'Form-field-Cluster-full_name')
             ->press('Create')
             ->waitForFlashMessage();
        return $this;
    }

    public function createPlan($name)
    {
        $this->visit(TEST_SELENIUM_BACKEND_URL.'/initbiz/cumuluscore/plans/create')
             ->type($name, 'Form-field-Plan-name')
             ->press('Create')
             ->waitForFlashMessage();
        return $this;
    }

    public function activateUser($email)
    {
        $this->visit(TEST_SELENIUM_BACKEND_URL.'/rainlab/user/users')
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

    public function attachClusterToPlan($plan, $clusterName)
    {
        $clusterId = $this->getRecordID($clusterName, TEST_SELENIUM_BACKEND_URL.'/initbiz/cumuluscore/clusters');
        $this->visit(TEST_SELENIUM_BACKEND_URL.'/initbiz/cumuluscore/clusters/update/' . $clusterId)
             ->findAndClickElement('Relation-formPlan-plan')
             ->hold(3)
             ->findAndClickElement($plan, "//li[contains(., '{$plan}')]")
             ->press('Save')
             ->waitForFlashMessage();
        return $this;
    }

    public function addUserToCluster($userEmail, $clusterName)
    {
        $userId = $this->getRecordID($userEmail, TEST_SELENIUM_BACKEND_URL.'/rainlab/user/users');
        $this->visit(TEST_SELENIUM_BACKEND_URL.'/rainlab/user/users/update/' . $userId)
             ->findAndClickElement('Clusters', '//a[@title="Clusters"]')
             ->hold(2)
             ->clickLabel($clusterName)
             ->hold(2)
             ->press('Save')
             ->waitForFlashMessage();
        return $this;
    }

    public function addModuleToPlan($module, $plan)
    {
        $planId = $this->getRecordID($plan, TEST_SELENIUM_BACKEND_URL.'/initbiz/cumuluscore/plans');
        $this->visit(TEST_SELENIUM_BACKEND_URL.'/initbiz/cumuluscore/plans/update/' . $planId)
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
             ->deleteAllCluster()
             ->deleteAllPlans();
        return $this;
    }

    public function deleteAllUsers()
    {
        $this->visit(TEST_SELENIUM_BACKEND_URL.'/rainlab/user/users')
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

    public function deleteAllCluster()
    {
        $this->visit(TEST_SELENIUM_BACKEND_URL.'/initbiz/cumuluscore/clusters')
             ->hold(2)
             ->findAndClickElement('check all clusters', "/html/body/div[1]/div/div[2]/div/div[2]/div/div/div/div[2]/div/table/thead/tr/th[1]")
             ->press('Delete selected')
             ->waitForElementsWithClass('sweet-alert')
             ->hold(2)
             ->press('OK')
             ->waitForFlashMessage()
             ->hold(2);
        return $this;
    }

    public function deleteAllPlans(){
        $this->visit(TEST_SELENIUM_BACKEND_URL.'/initbiz/cumuluscore/plans')
            ->hold(2)
            ->findAndClickElement('check all plans', "/html/body/div[1]/div/div[2]/div/div[2]/div/div/div/div[2]/div/table/thead/tr/th[1]")
            ->press('Delete selected')
            ->waitForElementsWithClass('sweet-alert')
            ->hold(2)
            ->press('OK')
            ->waitForFlashMessage()
            ->hold(2);

    }


}