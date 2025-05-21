<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateInitbizCumuluscorePlansTable2 extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_plans', function ($table) {
            $table->renameColumn('plan_id', 'id');
        });

        Schema::table('initbiz_cumuluscore_plans', function ($table) {
            $table->mediumText('features')->nullable();
        });
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_plans', function ($table) {
            $table->renameColumn('id', 'plan_id');
            $table->dropColumn('features');
        });
    }
}
