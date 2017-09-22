<?php

use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class EnterSystemPagesTest extends Ui2TestCase {

    /**
     * @test *
     * * @return void
     */
    public function guest_cannot_enter_choose_company_page()
    {
        $this->visit('/system/choose-company')
        ->see('Forbidden');
    }

}