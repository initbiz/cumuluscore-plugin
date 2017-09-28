<?php

use InitBiz\Selenium2Tests\Classes\Ui2TestCase;

class GuestAccessPagesTest extends Ui2TestCase {

    /**
     * @test *
     * * @return void
     */
    public function guest_cannot_enter_choose_company_page()
    {
        $this->visit('/system/choose-company')
        ->see('Forbidden');
    }

    /**
     * @test *
     * * @return void
     */
    public function guest_cannot_enter_company_dashboard_page()
    {

    }

    /**
     * @test *
     * * @return void
     */
    public function guest_cannot_enter_module_guarded_page()
    {

    }

    /**
     * @test *
     * * @return void
     */
    public function guest_can_enter_public_pages()
    {

    }

}