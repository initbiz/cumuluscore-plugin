<?php namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateInitbizCumuluscoreClustersTable extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->renameColumn('cluster_id', 'id');
            $table->renameColumn('full_name', 'name');
        });
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->renameColumn('id', 'cluster_id');
            $table->renameColumn('name', 'full_name');
        });
    }
}
