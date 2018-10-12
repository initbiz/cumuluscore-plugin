<?php namespace Initbiz\Bemygestsellers\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateInitbizCumuluscoreModulesTable extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_modules', function ($table) {
            $table->unique('slug');
        });
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_modules', function ($table) {
            $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($table->getTable());
            $index_name = 'initbiz_cumuluscore_modules_slug__unique';

            if (array_key_exists($index_name, $indexes)) {
                $table->dropUnique($index_name);
            }
        });
    }
}
