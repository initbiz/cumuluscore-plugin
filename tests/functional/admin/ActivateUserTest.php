<?php
use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class ActivateUserTest extends Ui2TestCase {


    use CustomDataProviders,
        OctoberSeleniumHelpers,
        CumulusHelpers,
        SeleniumHelpers;
    /**
     * @test *
     * @dataProvider providerUserData
     * * @return void
     */
    public function admin_can_activate_user($data)
    {
       $this->signInToBackend()
            ->createUser($data)
            ->activateUser($data['email'])
            ->hold(1)
            ->see('User has been activated');
    }
}