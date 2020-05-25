<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassengersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passengers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('id_type', ['china_id', 'hkmo_pass', 'tw_pass', 'passport']);
            $table->string('id_num');
            $table->enum('gender', ['male', 'female']);
            $table->string('mobile');
            $table->enum('type', ['adult', 'child', 'student', 'military']);
            $table->integer('user_id');
            $table->boolean('is_self')->default(false);
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
        Schema::dropIfExists('passengers');
    }
}
