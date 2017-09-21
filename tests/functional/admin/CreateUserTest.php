<?php

use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class CreateUserTest extends Ui2TestCase {

    use CumulusDataProviders,
        CumulusHelpers,
        OctoberSeleniumHelpers;
        
    /**
     * @test *
     * @dataProvider providerUserData
     * * @return void
     */
    public function can_create_user($data)
    {
        $this->createUser($data)
            ->waitForElementsWithClass('flash-message')
            ->hold(1)
            ->see('User created');
    }
}