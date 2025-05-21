<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateInitbizCumuluscoreClustersTable extends Migration
{
    public function up()
    {
        Schema::table('initbiz_cumuluscore_clusters', function ($table) {
            $table->string('full_name')->nullable(false)->unsigned(false)->default(null)->change();
            $table->string('slug')->nullable(false)->unsigned(false)->default(null)->change();
        });
    }

    public function down()
    {
    }
}
