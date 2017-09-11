<?php namespace InitBiz\CumulusCore\Tests\Models;

use  \InitBiz\CumulusCore\Tests\Traits\SetUp;

class CompanyTest extends \PluginTestCase  {

    use SetUp;
    private $company;


    public function setUp()
    {
        parent::setUp();
        $this->company = $this->createCompany();
    }

    /** * @test * * @return void */

    public function company_has_not_empty_properties()
    {
        $this->assertNotEmpty($this->company->full_name);
        $this->assertNotEmpty($this->company->slug);
    }
}