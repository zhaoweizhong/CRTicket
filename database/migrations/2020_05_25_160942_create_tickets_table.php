<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('train_id');
            $table->date('date');
            $table->integer('depart_station_id');
            $table->integer('arrive_station_id');
            $table->integer('depart_station_num');
            $table->integer('arrive_station_num');
            $table->integer('passenger_id');
            $table->enum('seat_type', ['SZ', '1Z', '2Z', 'YZ', 'YW', 'RW']);
            $table->integer('carriage');
            $table->string('seat');
            $table->float('price');
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
        Schema::dropIfExists('tickets');
    }
}
