<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainResource extends JsonResource
{
    public $preserveKeys = true;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data =  [];
        if (isset($this->id)) {
            $data['id'] = $this->id;
        }
        if (isset($this->numbers)) {
            $data['numbers'] = $this->numbers;
        }
        if (isset($this->price)) {
            $data['price'] = $this->price;
        }
        if (isset($this->status)) {
            $data['status'] = $this->status;
        }
        if (isset($this->stations)) {
            $data['stations'] = StationResource::collection($this->stations);
        }
        if (isset($this->created_at)) {
            $data['created_at'] = $this->created_at;
        }
        if (isset($this->updated_at)) {
            $data['updated_at'] = $this->updated_at;
        }
        if (isset($this->depart_station_id)) {
            $data['depart_station_id'] = $this->depart_station_id;
            $data['arrive_station_id'] = $this->arrive_station_id;
            $data['depart_station_num'] = $this->depart_station_num;
            $data['arrive_station_num'] = $this->arrive_station_num;
            $data['seats_availability'] = $this->seats_availability;
        } else if (isset($this->seats_availability)) {
            $data['seats_availability'] = $this->seats_availability;
        }

        return $data;
    }
}
