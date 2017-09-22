<?php
use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class ActivateUserTest extends Ui2TestCase {


    use CustomDataProviders,
        OctoberSeleniumHelpers,
        CumulusHelpers;
    /**
     * @test *
     * @dataProvider providerUserData
     * * @return void
     */
    public function can_activate_user($data)
    {
       $this->signInToBackend()
            ->createUser($data)
            ->visit('/panel/rainlab/user/users')
            ->findElement('Activated', '/html/body/div[1]/div/div[2]/div/div[2]/div/div/div/div[2]/div')
            ->click();
        $this->hold(1)
            ->findElement('user', '/html/body/div[1]/div/div[2]/div/div[2]/div/div/div/div[3]/div/table/tbody/tr[1]')
            ->click();
        $this->findElement('active user', '/html/body/div[1]/div/div[2]/div/div[2]/div/div/div/form/div/div[1]/div/div[2]/div/div[1]/div/div/p/a')
            ->click();
        $this->waitForElementsWithClass('sweet-alert')
            ->hold(3)
            ->findElement('ok button', '/html/body/div[5]/div[2]/p[2]/button[2]')
            ->click();
        $this->hold(3)
            ->see('User has been activated');

    }
}