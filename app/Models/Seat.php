<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = [
        'train_id', 'status', 'date'
    ];

    public function train() {
        return $this->belongsTo(Train::class);
    }

    public static function getAvailable(Train $train, $date, $type, Station $depart_station, Station $arrive_station) {
        $tickets = $train->tickets()->whereDate('date', $date)->where('seat_type', $type)
                    ->where('depart_station_num', '<=', $train->stations()->where('station_id', $depart_station->id)->first()->pivot->station_num)
                    ->where('arrive_station_num', '>=', $train->stations()->where('station_id', $arrive_station->id)->first()->pivot->station_num);
        $total_seats = [];
        if (substr(json_decode($train->numbers)[0],0,1) == 'G' || substr(json_decode($train->numbers)[0],0,1) == 'D') {
            $total_seats = Train::$DG_total;
        } else if (substr(json_decode($train->numbers)[0],0,1) == 'C') {
            $total_seats = Train::$C_total;
        } else {
            $total_seats = Train::$Other_total;
        }
        $total = $total_seats[$type];
        return $total - $tickets->count();
    }

    public static function getAvailableArray(Train $train, $date, Station $depart_station, Station $arrive_station) {
        if (substr(json_decode($train->numbers)[0],0,1) == 'G' || substr(json_decode($train->numbers)[0],0,1) == 'D') {
            return [
                'SZ' => Seat::getAvailable($train, $date, 'SZ', $depart_station, $arrive_station),
                '1Z' => Seat::getAvailable($train, $date, '1Z', $depart_station, $arrive_station),
                '2Z' => Seat::getAvailable($train, $date, '2Z', $depart_station, $arrive_station),
            ];
        } else if (substr(json_decode($train->numbers)[0],0,1) == 'C') {
            return [
                '1Z' => Seat::getAvailable($train, $date, '1Z', $depart_station, $arrive_station),
                '2Z' => Seat::getAvailable($train, $date, '2Z', $depart_station, $arrive_station),
            ];
        } else {
            return [
                'YZ' => Seat::getAvailable($train, $date, 'YZ', $depart_station, $arrive_station),
                'RW' => Seat::getAvailable($train, $date, 'RW', $depart_station, $arrive_station),
                'YW' => Seat::getAvailable($train, $date, 'YW', $depart_station, $arrive_station),
            ];
        }
    }

    public static function getAvailableSeatNumber(Train $train, $depart_station_num, $arrive_station_num, $date, $type) {
        $seats_num = [];
        if (substr(json_decode($train->numbers)[0],0,1) == 'G' || substr(json_decode($train->numbers)[0],0,1) == 'D') {
            $seats_num = Train::$DG_seats_num;
        } else if (substr(json_decode($train->numbers)[0],0,1) == 'C') {
            $seats_num = Train::$C_seats_num;
        } else {
            $seats_num = Train::$Other_seats_num;
        }

        for ($carriage = 1; $carriage <= count($seats_num); $carriage++) {
            if (array_key_exists($type, $seats_num[$carriage - 1])) {
                foreach ($seats_num[$carriage - 1][$type] as $seat) {
                    if ($seat_m = $train->seats()->whereDate('date', $date)->first() == null) {
                        return [$carriage, $seat];
                    } else {
                        $seat_status = $seat_m->status;
                        // $digit_num = $train->stations()->count() - 1;
                        $demand_sets_value = 0;
                        for ($i=$depart_station_num; $i < $arrive_station_num; $i++) { 
                            $demand_sets_value = $demand_sets_value + pow(2, $i);
                        }
                        $seat_status_value = bindec($seat_status);
                        $value = $seat_status_value & $demand_sets_value;
                        if ($value == 0) {
                            return [$carriage, $seat];
                        }
                    }

                }
            }
        }
    }
}
