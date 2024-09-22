<?php

namespace Initbiz\CumulusCore\Updates;

use Schema;
use Illuminate\Support\Facades\DB;
use October\Rain\Database\Updates\Migration;

class UpdateInitbizCumuluscoreClustersTable2 extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->string('thoroughfare')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('account_number')->nullable();
        });

        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('rainlab_location_countries');
        });
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->dropColumn('thoroughfare');
            $table->dropColumn('city');
            $table->dropColumn('phone');
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
            $table->dropColumn('postal_code');
            $table->dropColumn('description');
            $table->dropColumn('email');
            $table->dropColumn('tax_number');
            $table->dropColumn('account_number');
        });
    }
}
