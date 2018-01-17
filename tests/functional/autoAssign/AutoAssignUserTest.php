<?php

use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class AutoAssignUserTest extends Ui2TestCase
{

    use Initbiz\CumulusCore\Traits\CumulusTestHelpers;
    use Initbiz\CumulusCore\Traits\CumulusDataProviders;

    /**
     * @test *
     * @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function user_auto_assign_to_new_cluster($userData, $clusterData)
    {

        $this->signInToBackend()
             ->visit( TEST_SELENIUM_BACKEND_URL.'/system/settings/update/initbiz/cumuluscore/auto_assign')
             ->hold(2)
             ->checkSwitchOn('Form-field-Settings-enable_auto_assign_user')
             ->findAndClickElement('select2-Form-field-Settings-auto_assign_user-container')
             ->hold(2)
             ->findAndClickElement('CreateNewCluster', "//li[contains(.,'Create new cluster')]")
             ->press('Save')
             ->visit('/register')
             ->type($userData['username'], 'name')
             ->type($userData['email'], 'email')
             ->type($userData['password'], 'password')
             ->type($clusterData['name'], 'clustername')
             ->press('Register');
        $userId = $this->getRecordID($userData['email'], TEST_SELENIUM_BACKEND_URL.'/rainlab/user/users');
        $this->visit(TEST_SELENIUM_BACKEND_URL.'/rainlab/user/users/preview/' . $userId)
             ->hold(2)
             ->findAndClickElement('Clusters', '//a[@title="Clusters"]')
             ->see($clusterData['name']);
    }

    /**
     * @test *
     * @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function user_auto_assign_to_existing_cluster($userData, $clusterData)
    {
        $this->signInToBackend()
            ->createCluster($clusterData)
            ->visit( TEST_SELENIUM_BACKEND_URL.'/system/settings/update/initbiz/cumuluscore/auto_assign')
            ->checkSwitchOn('Form-field-Settings-enable_auto_assign_user')
            ->findAndClickElement('select2-Form-field-Settings-auto_assign_user-container')
            ->findAndClickElement('CreateNewCluster', "//li[contains(.,'Choose existing cluster')]")
            ->findAndClickElement('CLuster picker', '//*[@id="select2-Form-field-Settings-auto_assign_user_concrete_cluster-container"]')
            ->findAndClickElement($clusterData['name'], "//li[contains(.,'{$clusterData['name']}')]")
            ->press('Save')
            ->visit('/register')
            ->type($userData['username'], 'name')
            ->type($userData['email'], 'email')
            ->type($userData['password'], 'password')
            ->press('Register');
        $userId = $this->getRecordID($userData['email'], TEST_SELENIUM_BACKEND_URL.'/rainlab/user/users');
        $this->visit(TEST_SELENIUM_BACKEND_URL.'/rainlab/user/users/preview/' . $userId)
            ->findAndClickElement('Clusters', '//a[@title="Clusters"]')
            ->see($clusterData['name']);

    }

    /**
     * @test *
     * @dataProvider providerUserData
     * * @return void
     */
    public function user_auto_assign_to_group($userData)
    {
        $this->signInToBackend()
             ->visit( TEST_SELENIUM_BACKEND_URL.'/system/settings/update/initbiz/cumuluscore/auto_assign')
             ->checkSwitchOn('Form-field-Settings-enable_auto_assign_user_to_group')
             ->findAndClickElement('select2-Form-field-Settings-group_to_auto_assign_user-container')
             ->findAndClickElement('CreateNewCluster', "//li[contains(.,'Registered')]")
             ->press('Save')
             ->visit('/register')
            ->type($userData['username'], 'name')
            ->type($userData['email'], 'email')
            ->type($userData['password'], 'password')
            ->press('Register')
             ->hold(1);
        $userId = $this->getRecordID($userData['email'], TEST_SELENIUM_BACKEND_URL.'/rainlab/user/users');
        $this->visit(TEST_SELENIUM_BACKEND_URL.'/rainlab/user/users/preview/' . $userId)
            ->see('Registered');
    }

    /**
     * @test *
     * @dataProvider providerUserData
     * * @return void
     */
    public function user_auto_assign_to_cluster_from_variable($userData)
    {
        $this->signInToBackend()
            ->createCluster(['name' => 'Foo bar'])
            ->visit( TEST_SELENIUM_BACKEND_URL.'/system/settings/update/initbiz/cumuluscore/auto_assign')
            ->hold(2)
            ->checkSwitchOn('Form-field-Settings-enable_auto_assign_user')
            ->findAndClickElement('select2-Form-field-Settings-auto_assign_user-container')
            ->hold(2)
            ->findAndClickElement('CreateNewCluster', "//li[contains(.,'Get cluster from a variable')]")
            ->press('Save')
            ->visit('/register')
            ->type($userData['username'], 'name')
            ->type($userData['email'], 'email')
            ->type($userData['password'], 'password')
            ->press('Register')
            ->hold(2);
        $userId = $this->getRecordID($userData['email'], TEST_SELENIUM_BACKEND_URL.'/rainlab/user/users');
        $this->visit(TEST_SELENIUM_BACKEND_URL.'/rainlab/user/users/preview/' . $userId)
            ->hold(2)
            ->findAndClickElement('Clusters', '//a[@title="Clusters"]')
            ->see('Foo bar');
    }

    /**
     * @test *
     * @dataProvider providerUserWithClusterData
     * * @return void
     */
    public function cluster_auto_assign_to_existing_plan($userData, $clusterData)
    {

        $this->signInToBackend()
            ->visit( TEST_SELENIUM_BACKEND_URL.'/system/settings/update/initbiz/cumuluscore/auto_assign')
            ->hold(2)
            ->checkSwitchOn('Form-field-Settings-enable_auto_assign_user')
            ->findAndClickElement('select2-Form-field-Settings-auto_assign_user-container')
            ->hold(2)
            ->findAndClickElement('CreateNewCluster', "//li[contains(.,'Create new cluster')]")
            ->hold(3)
            ->findAndClickElement('Clustersi tab', '//a[@title="Auto assign clusters"]')
            ->checkSwitchOn('Form-field-Settings-enable_auto_assign_cluster')
            ->findAndClickElement('select2-Form-field-Settings-auto_assign_cluster-container')
            ->findAndClickElement('ChoosePlan', "//li[contains(.,'Choose plan')]")
            ->findAndClickElement('select2-Form-field-Settings-auto_assign_cluster_concrete_plan-container')
            ->findAndClickElement('ChoosePlan', "//li[contains(.,'Free plan')]")
            ->press('Save')
            ->visit('/register')
            ->type($userData['username'], 'name')
            ->type($userData['email'], 'email')
            ->type($userData['password'], 'password')
            ->type($clusterData['name'], 'clustername')
            ->press('Register');
        $userId = $this->getRecordID($userData['email'], TEST_SELENIUM_BACKEND_URL.'/rainlab/user/users');
        $this->visit(TEST_SELENIUM_BACKEND_URL.'/rainlab/user/users/preview/' . $userId)
            ->hold(2)
            ->findAndClickElement('Clusters', '//a[@title="Clusters"]')
            ->see($clusterData['name']);
        $clusterId = $this->getRecordID($clusterData['name'], TEST_SELENIUM_BACKEND_URL.'/initbiz/cumuluscore/clusters/');
        $this->visit(TEST_SELENIUM_BACKEND_URL.'/rainlab/user/users/preview/' . $clusterId)
            ->see('Free plan');

    }

}
