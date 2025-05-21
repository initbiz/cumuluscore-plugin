<?php

declare(strict_types=1);
use Initbiz\Selenium2tests\Classes\Ui2TestCase;

class RegisterTest extends Ui2TestCase
{
    use Initbiz\CumulusCore\Traits\CumulusTestHelpers;
    use Initbiz\CumulusCore\Traits\CumulusDataProviders;

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
             ->hold(2)
             ->seePageIs('/');
        //sign in to backed for clearCumulus
        $this->signInToBackend();
    }

    protected function afterTest()
    {
        $this->hold(2)
             ->clearCumulus();
    }
}
