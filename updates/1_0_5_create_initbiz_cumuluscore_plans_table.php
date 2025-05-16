<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateInitbizCumuluscorePlansTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('initbiz_cumuluscore_plans')) {
            Schema::create('initbiz_cumuluscore_plans', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('plan_id');
                $table->string('name');
                $table->string('slug')->unique();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('initbiz_cumuluscore_plans');
    }
}
