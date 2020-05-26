<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $fillable = [
        'name', 'quanpin', 'jianpin', 'city', 'status'
    ];

    public function tickets_depart() {
        return $this->hasMany(Ticket::class, 'depart_station_id', 'id');
    }

    public function tickets_arrive() {
        return $this->hasMany(Ticket::class, 'arrive_station_id', 'id');
    }

    public function trains() {
        return $this->belongsToMany(Train::class, 'train_station', 'station_id', 'train_id')
             ->withPivot('station_num', 'train_num', 'arrive_time', 'depart_time')
             ->withTimestamps();
    }
}
