<?php

declare(strict_types=1);
use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class ActivateUserTest extends Ui2TestCase
{
    use Initbiz\Selenium2tests\Traits\CustomDataProviders;
    use Initbiz\Selenium2tests\Traits\OctoberSeleniumHelpers;
    use Initbiz\CumulusCore\Traits\CumulusTestHelpers;
    use Initbiz\Selenium2tests\Traits\SeleniumHelpers;

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

    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }
}
