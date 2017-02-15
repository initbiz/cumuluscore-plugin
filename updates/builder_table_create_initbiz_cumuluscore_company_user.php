<?php namespace InitBiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateInitbizCumuluscoreCompanyUser extends Migration
{
    public function up()
    {
        Schema::create('initbiz_cumuluscore_company_user', function($table)
        {
            $table->integer('user_id')->unsigned();
            $table->integer('company_id')->unsigned();
            $table->primary(['user_id','company_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('initbiz_cumuluscore_company_user');
    }
}