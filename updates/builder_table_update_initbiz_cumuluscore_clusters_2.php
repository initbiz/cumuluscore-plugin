<?php namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateInitbizCumuluscoreClusters2 extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_clusters', function($table)
        {
            $table->string('thoroughfare')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->integer('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('rainlab_location_countries');
            $table->string('postal_code')->nullable();
            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('account_number')->nullable();
        });
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_clusters', function($table)
        {
            $table->dropColumn('thoroughfare');
            $table->dropColumn('city');
            $table->dropColumn('phone');
            $table->dropForeign('initbiz_cumuluscore_clusters_country_id_foreign');
            $table->dropColumn('country_id');
            $table->dropColumn('postal_code');
            $table->dropColumn('description');
            $table->dropColumn('email');
            $table->dropColumn('tax_number');
            $table->dropColumn('account_number');
        });
    }
}
