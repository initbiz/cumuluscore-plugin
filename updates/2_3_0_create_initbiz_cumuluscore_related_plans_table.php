<?php namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateInitbizCumuluscoreRelatedPlansTable extends Migration
{
    public function up()
    {
        Schema::create('initbiz_cumuluscore_related_plans', function(Blueprint $table) {
            $table->integer('plan_id')->unsigned();
            $table->integer('related_plan_id')->unsigned();
            $table->primary(['plan_id', 'related_plan_id']);
            $table->string('relation')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('initbiz_cumuluscore_related_plans');
    }
}
