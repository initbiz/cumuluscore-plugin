<?php namespace InitBiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateInitbizCumuluscoreClusterModule extends Migration
{
    public function up()
    {
        Schema::create('initbiz_cumuluscore_cluster_module', function ($table) {
            $table->integer('cluster_id')->unsigned();
            $table->integer('module_id')->unsigned();
            $table->primary(['cluster_id','module_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('initbiz_cumuluscore_cluster_module');
    }
}
