<?php

namespace Initbiz\CumulusCore\Updates;

use October\Rain\Database\Updates\Migration;
use Initbiz\CumulusCore\Console\ExtractClusterKeysFile;

class MakeClusterSlugClusterIdInFeatureLogsTable extends Migration
{
    public function up()
    {
        $command = new ExtractClusterKeysFile();
        $command->handle();
    }

    public function down()
    {
        // Do nothing;
    }
}
