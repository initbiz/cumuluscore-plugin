<?php namespace InitBiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateInitbizCumuluscoreCompanyModule extends Migration
{
    public function up()
    {
        Schema::create('initbiz_cumuluscore_company_module', function($table)
        {
            $table->integer('company_id')->unsigned();
            $table->integer('module_id')->unsigned();
            $table->primary(['company_id','module_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('initbiz_cumuluscore_company_module');
    }
}
