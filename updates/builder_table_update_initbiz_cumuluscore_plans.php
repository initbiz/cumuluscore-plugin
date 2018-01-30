<?php namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateInitbizCumuluscorePlans extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_plans', function($table)
        {
            $table->string('slug')->nullable(false)->unsigned(false)->default(null)->change();
        });
    }

    public function down()
    {
    }
}
