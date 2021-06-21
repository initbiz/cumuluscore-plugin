<?php

namespace Initbiz\CumulusCore\Traits;

trait CumulusTestHelpers
{
    public function createUser($data)
    {
        $this->gotoBackend('initbiz/cumuluscore/users/create');

        $this->type($data['name'], 'Form-field-User-name')
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
        $this->gotoBackend('initbiz/cumuluscore/clusters/create');

        $this->type($data['name'], 'Form-field-Cluster-full_name')
            ->press('Create')
            ->waitForFlashMessage();
        return $this;
    }

    public function createPlan($name)
    {
        $this->gotoBackend('initbiz/cumuluscore/plans/create');

        $this->type($name, 'Form-field-Plan-name')
            ->press('Create')
            ->waitForFlashMessage();

        return $this;
    }

    public function activateUser($email)
    {
        $this->gotoBackend('initbiz/cumuluscore/users/create');

        $this->clickRowInBackendList($email)
            ->clickLink('Activate this user manually')
            ->waitForElementsWithClass('sweet-alert')
            ->hold(1)
            ->press('OK')
            ->waitForFlashMessage();
        return $this;
    }


    public function signInToFrontend($data, $url = "/")
    {
        $this->visit($url)
            ->type($data['email'], 'login')
            ->type($data['password'], 'password')
            ->findAndClickElement("Login button", "//button[@type='submit']")
            ->hold(2);
        return $this;
    }

    public function attachClusterToPlan($clusterName, $plan)
    {
        $this->gotoBackend('initbiz/cumuluscore/clusters');

        $this->clickRowInBackendList($clusterName)
            ->select2('select2-Form-field-Cluster-plan-container', $plan)
            ->press('Save');

        return $this;
    }

    public function addUserToCluster($userEmail, $clusterName)
    {
        $this->gotoBackend('initbiz/cumuluscore/users');
        $this->clickRowInBackendList($userEmail)
            ->findAndClickElement('Clusters', '//a[@title="Clusters"]')
            ->hold(2)
            ->clickLabel($clusterName)
            ->hold(2)
            ->press('Save')
            ->waitForFlashMessage();
        return $this;
    }

    public function addFeatureToPlan($feature, $plan)
    {
        $this->gotoBackend('initbiz/cumuluscore/plans');
        $this->clickRowInBackendList($plan)
            ->clickLabel($feature);
        $this->hold(1)
            ->press('Save')
            ->waitForFlashMessage();
        return $this;
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
        $this->gotoBackend('initbiz/cumuluscore/users');
        $this->typeInBackendSearch('', true)
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

    public function deleteAllClusters()
    {
        $this->gotoBackend('initbiz/cumuluscore/clusters');
        $this->findAndClickElement('check all clusters', "/html/body/div[1]/div/div[2]/div/div[2]/div/div/div/div[2]/div/table/thead/tr/th[1]")
            ->press('Delete selected')
            ->waitForElementsWithClass('sweet-alert')
            ->hold(2)
            ->press('OK')
            ->waitForFlashMessage()
            ->hold(2);
        return $this;
    }

    public function deleteAllPlans()
    {
        $this->gotoBackend('initbiz/cumuluscore/plans');
        $this->findAndClickElement('check all plans', "/html/body/div[1]/div/div[2]/div/div[2]/div/div/div/div[2]/div/table/thead/tr/th[1]")
            ->press('Delete selected')
            ->waitForElementsWithClass('sweet-alert')
            ->hold(2)
            ->press('OK')
            ->waitForFlashMessage()
            ->hold(2);
    }

    public function gotoCumulusBackend($sidenavLabel = "")
    {
        $this->gotoBackend('initbiz/cumuluscore/dashboard');

        if ($sidenavLabel !== "") {
            $this->findAndClickElement($sidenavLabel, "//a[contains(., '" . $sidenavLabel . "')]");
        }
    }

    public function gotoCumulusBackendForm($controllerName, $context = "create", $id = null)
    {
        if ($context !== "create") {
            if ($id === null) {
                //TODO: throw exception
                return;
            }
        }

        $this->signInToBackend();

        $url = $this->backendUrl . '/initbiz/cumuluscore/' . $controllerName . '/' . $context;

        if ($context === "preview" || $context === "update") {
            $url .= $id;
        }
    }
}
