<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('UserID');
            $table->string('LoginID');
            $table->string('Salt');
            $table->string('Password');
            $table->string('LastLoginDateTime');
            $table->string('AllowLogin');
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
        Schema::dropIfExists('user');
    }
}
