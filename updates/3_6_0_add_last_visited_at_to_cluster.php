<?php

namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddLastVisitedAtToCluster extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->timestamp('last_visited_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->dropColumn('last_visited_at');
        });
    }
}
