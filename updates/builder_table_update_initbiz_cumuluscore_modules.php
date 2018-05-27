<?php namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateInitbizCumuluscoreModules extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_modules', function($table)
        {
            $table->text('description')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('initbiz_cumuluscore_modules', function($table)
        {
            $table->dropColumn('description');
        });
    }
}
