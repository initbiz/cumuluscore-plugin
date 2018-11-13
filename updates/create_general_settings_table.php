<?php namespace Initbiz\CumulusCore\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateGeneralSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('initbiz_cumuluscore_general_settings', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('initbiz_cumuluscore_general_settings');
    }
}
