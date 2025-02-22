<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location', function (Blueprint $table) {
            $table->increments('LocationID');
            $table->integer('LocationType');
            $table->integer('UserType');
            $table->integer('UserID');
            $table->string('LocationName');
            $table->string('LocationDESC');
            $table->string('LocationNum');
            $table->string('LocationPrimary');
            $table->string('Fax');
            $table->string('Email');
            $table->string('EmailTechnical');
            $table->string('Address1');
            $table->string('Address2');
            $table->string('City');
            $table->string('PostalCode');
            $table->string('State');
            $table->string('CountryCode');
            $table->string('Lat');
            $table->string('Lon');
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
        Schema::dropIfExists('location');
    }
}
