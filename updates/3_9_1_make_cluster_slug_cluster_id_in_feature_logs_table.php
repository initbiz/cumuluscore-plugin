<?php

namespace Initbiz\CumulusCore\Updates;

use Schema;
use Initbiz\CumulusCore\Models\Cluster;
use October\Rain\Database\Updates\Migration;
use Initbiz\Cumuluscore\Models\ClusterFeatureLog;

class MakeClusterSlugClusterIdInFeatureLogsTable extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_cluster_feature_logs', function ($table) {
            $table->integer('cluster_id')->unsigned()->nullable();
        });

        $logs = ClusterFeatureLog::all();
        $clusters = Cluster::all(['id', 'slug'])->keyBy('slug')->toArray();

        foreach ($logs as $log) {
            $cluster = $clusters[$log->cluster_slug];
            $log->cluster_id = $cluster['id'];
            $log->save();
        }

        Schema::table('initbiz_cumuluscore_cluster_feature_logs', function ($table) {
            $table->dropColumn('cluster_slug');
        });

        Schema::table('initbiz_cumuluscore_cluster_feature_logs', function ($table) {
            $table->integer('cluster_id')->unsigned()->nullable(false)->change();
        });

        Schema::table('initbiz_cumuluscore_cluster_feature_logs', function ($table) {
            $table->foreign('cluster_id', 'initbiz_cumuluscore_cluster_feature_logs_cluster_id')
                ->references('id')->on('initbiz_cumuluscore_clusters');

            $table->index('feature_code', 'initbiz_cumuluscore_cluster_feature_logs_feature_code');
            $table->index('action', 'initbiz_cumuluscore_cluster_feature_logs_action');
        });
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_cluster_feature_logs', function ($table) {
            $table->dropForeign('initbiz_cumuluscore_cluster_feature_logs_cluster_id');
            $table->dropIndex('initbiz_cumuluscore_cluster_feature_logs_feature_code');
            $table->dropIndex('initbiz_cumuluscore_cluster_feature_logs_action');
        });

        Schema::table('initbiz_cumuluscore_cluster_feature_logs', function ($table) {
            $table->string('cluster_slug')->nullable();
        });

        $logs = ClusterFeatureLog::all();
        $clusters = Cluster::all(['id', 'slug'])->keyBy('slug')->toArray();

        foreach ($logs as $log) {
            $cluster = $clusters[$log->cluster_id];
            $log->cluster_slug = $cluster['slug'];
            $log->save();
        }

        Schema::table('initbiz_cumuluscore_cluster_feature_logs', function ($table) {
            $table->dropColumn('cluster_id');
        });
    }
}
