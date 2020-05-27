<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainStationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('train_station', function (Blueprint $table) {
            $table->id();
            $table->integer('train_id');
            $table->integer('station_id');
            $table->integer('station_num'); // 此站在列车全程的位置序号
            $table->string('train_num'); // 列车在此站的车次
            $table->time('arrive_time')->nullable();
            $table->time('depart_time')->nullable();
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
        Schema::dropIfExists('train_station');
    }
}
