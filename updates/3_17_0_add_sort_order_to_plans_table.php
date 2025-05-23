<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Updates;

use Schema;
use Initbiz\CumulusCore\Models\Plan;
use October\Rain\Database\Updates\Migration;

class AddSortOrderToPlansTable extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_plans', function ($table) {
            $table->integer('sort_order')->nullable();
        });

        (new Plan())->resetSortableOrdering();
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_plans', function ($table) {
            $table->dropColumn('sort_order');
        });
    }
}
