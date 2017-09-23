<?php

use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class SignInTest extends Ui2TestCase {

    use CumulusDataProviders,
        CumulusHelpers,
        OctoberSeleniumHelpers;
    /**
     * @test *
     * @dataProvider providerUserData
     * * @return void
     */
    public function active_user_cannot_sign_in($data)
    {
        $this->signInToBackend()
            ->createUser($data)
            ->activateUser($data['email'])
            ->singIn($data)
            ->seePageIs('/system/choose-company');

    }
}