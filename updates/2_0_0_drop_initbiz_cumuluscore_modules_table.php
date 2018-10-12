<?php namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class DropInitbizCumuluscoreModulesTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('initbiz_cumuluscore_modules');
    }

    public function down()
    {
        Schema::create('initbiz_cumuluscore_modules', function ($table) {
            $table->increments('module_id')->unsigned();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
        });
    }
}
