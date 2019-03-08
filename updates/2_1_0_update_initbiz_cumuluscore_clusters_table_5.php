<?php namespace Initbiz\CumulusCore\Updates;

use Schema;
use Initbiz\CumulusCore\Models\Cluster;
use October\Rain\Database\Updates\Migration;

class UpdateInitbizCumuluscoreClustersTable5 extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->dropColumn('deleted_at');
        });
    }
}
