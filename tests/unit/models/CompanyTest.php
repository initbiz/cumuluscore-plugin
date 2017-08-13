<?php namespace InitBiz\CumulusCore\Tests\Unit\Models;

use InitBiz\CumulusCore\Models\Company;
use PluginTestCase;

class CompanyTest extends PluginTestCase
{
    public function tearDown()
    {
        Company::truncate();
        parent::tearDown();
    }

    /** * @test * * @return void */
    public function company_added()
    {
        $this->assertEquals(0, Company::count());
    }
}
