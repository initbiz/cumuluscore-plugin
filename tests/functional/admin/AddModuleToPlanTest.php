<?php

declare(strict_types=1);
use Initbiz\Selenium2Tests\Classes\Ui2TestCase;

class AddModuleToPlanTest extends Ui2TestCase
{
    use Initbiz\CumulusCore\Traits\CumulusDataProviders;
    use Initbiz\CumulusCore\Traits\CumulusTestHelpers;

    /**
     * @test *
     * * @return void
     */
    public function admin_can_add_module_to_plan()
    {
        $this->signInToBackend()
            ->createPlan('Example plan')
            ->addModuleToPlan('CumulusProducts', 'Example plan')
            ->see('Plans updated');
    }

    protected function afterTest()
    {
        $this->hold(2)
            ->clearCumulus();
    }
}
