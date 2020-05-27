<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'train_id' => $this->train_id,
            'date' => $this->date,
            'depart_station_id' => $this->depart_station_id,
            'arrive_station_id' => $this->arrive_station_id,
            'depart_station_num' => $this->depart_station_num,
            'arrive_station_num' => $this->arrive_station_num,
            'passenger_id' => $this->passenger_id,
            'seat_type' => $this->seat_type,
            'carriage' => $this->carriage,
            'seat' => $this->seat,
            'price' => $this->price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'train' => new TrainResource($this->train),
            'stations' => new TrainResource($this->train->stations),
            'passenger' => new PassengerResource($this->passenger),
            'depart_station' => new StationResource($this->depart_station),
            'arrive_station' => new StationResource($this->arrive_station)
        ];
    }
}
