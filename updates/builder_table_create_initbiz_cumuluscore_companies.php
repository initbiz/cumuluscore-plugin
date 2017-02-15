<?php namespace InitBiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateInitbizCumuluscoreCompanies extends Migration
{
    public function up()
    {
        Schema::create('initbiz_cumuluscore_companies', function($table)
        {
            $table->increments('company_id')->unsigned();
            $table->text('full_name');
            $table->text('slug');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('initbiz_cumuluscore_companies');
    }
}
