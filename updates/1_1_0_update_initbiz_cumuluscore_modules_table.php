<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class UpdateInitbizCumuluscoreModulesTable extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_modules', function (Blueprint $table) {
            $indexName = 'initbiz_cumuluscore_modules_slug__unique';

            $connection = Schema::getConnection();
            // Laravel <11
            if (method_exists($connection, 'getDoctrineSchemaManager')) {
                $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($table->getTable());

                if (!array_key_exists($indexName, $indexes)) {
                    $table->unique('slug', $indexName);
                }
            } else {
                if (!Schema::hasIndex($table->getTable(), $indexName)) {
                    $table->unique('slug', $indexName);
                }
            }
        });
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_modules', function ($table) {
            $indexName = 'initbiz_cumuluscore_modules_slug__unique';
            $connection = Schema::getConnection();
            // Laravel <11
            if (method_exists($connection, 'getDoctrineSchemaManager')) {
                $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($table->getTable());

                if (array_key_exists($$indexName, $indexes)) {
                    $table->unique('slug', $indexName);
                }
            } else {
                if (Schema::hasIndex($table->getTable(), $indexName)) {
                    $table->unique('slug', $indexName);
                }
            }
        });
    }
}
