<?php namespace InitBiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateInitbizCumuluscoreClusterUser extends Migration
{
    public function up()
    {
        Schema::create('initbiz_cumuluscore_cluster_user', function ($table) {
            $table->integer('user_id')->unsigned();
            $table->integer('cluster_id')->unsigned();
            $table->primary(['user_id','cluster_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('initbiz_cumuluscore_cluster_user');
    }
}
