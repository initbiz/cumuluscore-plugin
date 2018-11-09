<?php namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateInitbizCumuluscorePlanModuleTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('initbiz_cumuluscore_plan_module')) {
            Schema::create('initbiz_cumuluscore_plan_module', function ($table) {
                $table->engine = 'InnoDB';
                $table->integer('plan_id');
                $table->integer('module_id');
                $table->primary(['plan_id' , 'module_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('initbiz_cumuluscore_plan_module');
    }
}
