<?php
use Initbiz\Selenium2tests\Classes\Ui2TestCase;

class RegisterTest extends Ui2TestCase {

    use CustomDataProviders;
    /**
     * @test *
     * * @return void
     * @dataProvider providerUserData

     */
    public function guest_can_register($data)
    {
        $this->visit('/register')
             ->type($data['name'], 'name')
             ->type($data['email'], 'email')
             ->type($data['password'], 'password')
             ->press('Register')
             ->hold(1)
             ->seePageIs('/');
    }
}