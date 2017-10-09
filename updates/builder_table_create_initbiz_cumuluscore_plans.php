<?php namespace InitBiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateInitbizCumuluscorePlans extends Migration
{
    public function up()
    {
        Schema::create('initbiz_cumuluscore_plans', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('plan_id');
            $table->string('name');
            $table->text('slug');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('initbiz_cumuluscore_plans');
    }
}
