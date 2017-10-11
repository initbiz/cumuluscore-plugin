<?php

use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class LoginWithWrongCredentialsTest extends Ui2TestCase {

    use CumulusHelpers,
        CumulusDataProviders;

    /**
     * @test *
     * * @return void
     */
    public function guest_cannot_login_to_frontend_with_empty_inputs()
    {
        $data = [
            'email' => '',
            'password' => '',
        ];
        $this->signInToFrontend($data)
             ->see('Something bad happened, mate, here it is');
    }

    /**
     * @test *
     * * @return void
     */
    public function guest_cannot_login_to_backend_with_empty_inputs()
    {
        $data = [
            'email' => '',
            'password' => '',
        ];
        $this->visit(TEST_SELENIUM_BACKEND_URL)
             ->type($data['email'], 'login')
             ->type($data['password'], 'password')
             ->findAndClickElement("Login button", "//button[@type='submit']")
             ->hold(1)
             ->seeFlash();
    }

    /**
     * @test *
     * * @return void
     */
    public function guest_cannot_login_to_frontend_with_wrong_credentials()
    {
        $this->signInToFrontend($this->fakeUserData())
             ->see('A user was not found with the given credentials');
    }

    /**
     * @test *
     * * @return void
     */
    public function guest_cannot_login_to_backend_with_wrong_credentials()
    {
        $data = $this->fakeUserData();
        $this->visit(TEST_SELENIUM_BACKEND_URL)
             ->type($data['name'], 'login')
             ->type($data['password'], 'password')
             ->findAndClickElement("Login button", "//button[@type='submit']")
             ->waitForFlashMessage()
             ->hold(1)
             ->see('A user was not found with the given credentials');
    }
}