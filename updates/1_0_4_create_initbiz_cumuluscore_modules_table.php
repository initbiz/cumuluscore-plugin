<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateInitbizCumuluscoreModulesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('initbiz_cumuluscore_modules')) {
            Schema::create('initbiz_cumuluscore_modules', function ($table) {
                $table->increments('module_id')->unsigned();
                $table->string('name');
                $table->string('slug');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('initbiz_cumuluscore_modules');
    }
}
