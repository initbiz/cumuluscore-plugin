<?php namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddAdditionalDataToCluster extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->mediumText('additional_data')->nullable();
        });
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->dropColumn('additional_data');
        });
    }
}
