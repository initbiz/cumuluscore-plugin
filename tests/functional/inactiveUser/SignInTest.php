<?php

use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class SignInTest extends Ui2TestCase {

    use CumulusDataProviders,
        CumulusHelpers;
    /**
     * @test *
     * @dataProvider providerUserData
     * * @return void
     */
    public function inactive_user_cannot_sign_in($data)
    {
        $this->signInToBackend()
             ->createUser($data)
             ->visit('/')
             ->singIn($data);
        //TODO: see if inactive user cannot login

    }
}