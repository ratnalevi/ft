<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Alerts', function (Blueprint $table) {
            $table->increments('AlertID');
            $table->string('AlertName');
            $table->string('AlertDescription');
            $table->string('RecordStatus');
            $table->string('InsertDateTime');
            $table->string('UpdateDateTime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_alerts');
    }
}
