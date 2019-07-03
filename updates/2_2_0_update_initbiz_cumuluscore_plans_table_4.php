<?php namespace Initbiz\CumulusCore\Updates;

use Schema;
use Initbiz\CumulusCore\Models\Plan;
use October\Rain\Database\Updates\Migration;

class UpdateInitbizCumuluscorePlansTable4 extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_plans', function ($table) {
            $table->boolean('is_registration_allowed')->nullable();
        });

        $plans = Plan::all();
        foreach ($plans as $plan) {
            $plan->is_registration_allowed = true;
            $plan->save();
        }
    }

    public function down()
    {
        Schema::table('initbiz_cumuluscore_plans', function ($table) {
            $table->dropColumn('is_registration_allowed');
        });
    }
}
