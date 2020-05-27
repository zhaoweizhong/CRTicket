<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'order_id', 'train_id', 'date', 'depart_station_id', 'arrive_station_id', 'depart_station_num', 'arrive_station_num', 'passenger_id', 'seat_type', 'carriage', 'seat', 'price'
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function train() {
        return $this->belongsTo(Train::class);
    }

    public function depart_station() {
        return $this->belongsTo(Station::class, 'depart_station_id', 'id');
    }

    public function arrive_station() {
        return $this->belongsTo(Station::class, 'arrive_station_id', 'id');
    }

    public function passenger() {
        return $this->belongsTo(Passenger::class);
    }
}
