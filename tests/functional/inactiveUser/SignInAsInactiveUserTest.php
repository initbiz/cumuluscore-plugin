<?php

use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class SignInAsInactiveUserTest extends Ui2TestCase {

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
             ->signInToFrontend($data)
             ->see('Something bad happened, mate, here it is: ');
    }
    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }
}