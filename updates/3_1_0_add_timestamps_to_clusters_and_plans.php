<?php namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddTimestampsToClustersAndPlans extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_plans', function ($table) {
            $table->timestamps();
        });

        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_plans', function ($table) {
            $table->dropColumn('updated_at');
            $table->dropColumn('created_at');
        });

        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->dropColumn('updated_at');
            $table->dropColumn('created_at');
        });
    }
}
