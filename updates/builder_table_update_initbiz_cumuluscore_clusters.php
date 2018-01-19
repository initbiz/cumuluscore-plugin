<?php namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateInitbizCumuluscoreClusters extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_clusters', function($table)
        {
            $table->string('full_name')->nullable(false)->unsigned(false)->default(null)->change();
            $table->string('slug')->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('initbiz_cumuluscore_clusters', function($table)
        {
            $table->text('full_name')->nullable(false)->unsigned(false)->default(null)->change();
            $table->text('slug')->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
}
