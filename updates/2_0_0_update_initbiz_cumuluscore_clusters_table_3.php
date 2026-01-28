<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateInitbizCumuluscoreClustersTable3 extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->renameColumn('cluster_id', 'id');
        });
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->renameColumn('full_name', 'name');
        });
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->renameColumn('id', 'cluster_id');
        });
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->renameColumn('name', 'full_name');
        });
    }
}
