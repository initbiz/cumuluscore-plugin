<?php namespace Initbiz\Bemygestsellers\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateInitbizCumuluscoreClusterUserTable extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_cluster_user', function ($table) {
            $table->renameColumn('user_id', 'user_id_tmp');
            $table->renameColumn('cluster_id', 'user_id');
        });
        Schema::table('initbiz_cumuluscore_cluster_user', function ($table) {
            $table->renameColumn('user_id_tmp', 'cluster_id');
        });
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_cluster_user', function ($table) {
            $table->renameColumn('user_id', 'user_id_tmp');
            $table->renameColumn('cluster_id', 'user_id');
        });
        Schema::table('initbiz_cumuluscore_cluster_user', function ($table) {
            $table->renameColumn('user_id_tmp', 'cluster_id');
        });
    }
}
