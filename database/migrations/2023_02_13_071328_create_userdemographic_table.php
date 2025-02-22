<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserdemographicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userdemographic', function (Blueprint $table) {
            $table->increments('UserID');
            $table->string('UserType');
            $table->string('FirstName');
            $table->string('Middle');
            $table->string('LastName');
            $table->unsignedBigInteger('LocationID');
            $table->unsignedBigInteger('ContactID');
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
        Schema::dropIfExists('userdemographic');
    }
}
