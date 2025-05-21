<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class DropInitbizCumuluscorePlanModulesTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('initbiz_cumuluscore_plan_module');
    }

    public function down()
    {
        Schema::create('initbiz_cumuluscore_plan_module', function ($table) {
            $table->engine = 'InnoDB';
            $table->integer('plan_id');
            $table->integer('module_id');
            $table->primary(['plan_id' , 'module_id']);
        });
    }
}
