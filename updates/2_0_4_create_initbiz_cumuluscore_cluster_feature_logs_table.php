<?php

declare(strict_types=1);

namespace Initbiz\Cumuluscore\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateClusterFeatureLogsTable extends Migration
{
    public function up()
    {
        Schema::create('initbiz_cumuluscore_cluster_feature_logs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('cluster_slug');
            $table->string('feature_code');
            $table->string('action');
            $table->timestamp('timestamp')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('initbiz_cumuluscore_cluster_feature_logs');
    }
}
