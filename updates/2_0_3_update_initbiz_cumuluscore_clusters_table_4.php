<?php namespace Initbiz\CumulusCore\Updates;

use Schema;
use Initbiz\CumulusCore\Models\Cluster;
use October\Rain\Database\Updates\Migration;

class UpdateInitbizCumuluscoreClustersTable4 extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('initbiz_cumuluscore_clusters', 'username')) {
            Schema::table('initbiz_cumuluscore_clusters', function ($table) {
                $table->string('username')->nullable()->unique();
            });

            if (!Schema::hasColumn('initbiz_cumuluscore_clusters', 'deleted_at')) {
                Schema::table('initbiz_cumuluscore_clusters', function ($table) {
                    $table->softDeletes();
                });
            }

            $clusters = Cluster::all();
            foreach ($clusters as $cluster) {
                $cluster->username = $cluster->slug;
                $cluster->save();
            }
        }
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($table->getTable());
            $index_name = 'initbiz_cumuluscore_clusters_username__unique';

            if (array_key_exists($index_name, $indexes)) {
                $table->dropUnique($index_name);
            }

            $table->dropColumn('username');
        });

        if (Schema::hasColumn('initbiz_cumuluscore_clusters', 'deleted_at')) {
            Schema::table('initbiz_cumuluscore_clusters', function ($table) {
                $table->dropColumn('deleted_at');
            });
        }
    }
}
