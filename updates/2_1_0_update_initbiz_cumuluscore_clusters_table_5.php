<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateInitbizCumuluscoreClustersTable5 extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('initbiz_cumuluscore_clusters', 'deleted_at')) {
            Schema::table('initbiz_cumuluscore_clusters', function ($table) {
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->dropColumn('deleted_at');
        });
    }
}
