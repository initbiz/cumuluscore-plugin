<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateInitbizCumuluscorePlansTable5 extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_plans', function ($table) {
            $table->integer('priority')->nullable();
            $table->boolean('is_trial')->default(false);
        });
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_plans', function ($table) {
            $table->dropColumn('priority');
            $table->dropColumn('is_trial');
        });
    }
}
