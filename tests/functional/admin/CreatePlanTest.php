<?php
use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class CreatePlanTest extends Ui2TestCase {

    use CumulusHelpers;
    /**
     * @test *
     * * @return void
     */
    public function admin_can_create_plan()
    {
        $this->signInToBackend()
             ->createPlan('Free')
             ->see('Plans created');

    }

    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }
}