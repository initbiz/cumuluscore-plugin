<?php

use Initbiz\Selenium2tests\Classes\Ui2TestCase ;

class SingInToBackendTest extends Ui2TestCase {

    /**
     * @test *
     * * @return void
     */
    public function sing_in_to_backend()
    {
        $this->signInToBackend()
             ->see('Dashboard');
    }
}