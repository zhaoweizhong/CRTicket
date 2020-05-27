<?php

namespace App\Http\Controllers;

use App\Http\Resources\StationResource;
use Illuminate\Http\Request;
use App\Models\Train;
use App\Http\Resources\TrainResource;
use App\Models\Seat;
use App\Models\Station;
use Illuminate\Database\Eloquent\Collection;

class TrainsController extends Controller
{
    public function index(Request $request, Train $train) {
        $query = $train->query();
        
        $trains = $query->where('status', 1)->paginate();

        return TrainResource::collection($trains);
    }

    public function search(Request $request) {
        $trains = Train::where('status', 1)->get();

        $depart_stations = [Station::find($request->depart_station)];
        $arrive_stations = [Station::find($request->arrive_station)];

        $date = $request->date;

        if (!$depart_stations[0]->status || !$arrive_stations[0]->status) {
            return response()->json([
                'status_code' => 404,
                'message' => '车站不存在'
            ])->setStatusCode(404);
        }

        // if ($city = $depart_stations[0]->city) {
        //     $depart_stations = Station::where('city', $city)->where('status', 1)->get();
        // }

        // if ($city = $arrive_stations[0]->city) {
        //     $arrive_stations = Station::where('city', $city)->where('status', 1)->get();
        // }

        $collection = [];
        $i = 0;
        foreach ($depart_stations as $depart_station) {
            foreach ($arrive_stations as $arrive_station) {
                foreach ($trains as $train) {
                    if ($train->where('status', 1) && $train->stations()->where('station_id', $depart_station->id)->exists() && $train->stations()->where('station_id', $arrive_station->id)->exists()
                        && (($depart_station_num = $train->stations()->where('station_id', $depart_station->id)->first()->pivot->station_num) < ($arrive_station_num = $train->stations()->where('station_id', $arrive_station->id)->first()->pivot->station_num))) {
                        $collection[$i] = $train;
                        $collection[$i]->seats_availability = Seat::getAvailableArray($train, $date, $depart_station, $arrive_station);
                        $collection[$i]->depart_station_id = $depart_station->id;
                        $collection[$i]->arrive_station_id = $arrive_station->id;
                        $collection[$i]->depart_station_num = $depart_station_num;
                        $collection[$i]->arrive_station_num = $arrive_station_num;
                        $i++;
                    }
                }
            }
        }

        return TrainResource::collection(new Collection($collection));
    }

    public function show(Request $request, Train $train) {
        if ($request->has('date') && $request->has('depart_station_id') && $request->has('arrive_station_id')) {
            $date = $request->date;
            $depart_station = Station::find($request->depart_station_id);
            $arrive_station = Station::find($request->arrive_station_id);
            $train->seats_availability = Seat::getAvailableArray($train, $date, $depart_station, $arrive_station);
        }
        return new TrainResource($train);
    }

    public function store(Request $request) {
        if ($request->user()->type != 'admin') {
            return response()->json([
                'status_code' => 403,
                'message' => '您无权进行此操作'
            ])->setStatusCode(403);
        }

        $this->validate($request, [
            'stations' => 'required|string',
            'train_numbers' => 'required|string',
            'times' => 'required|string',
            'prices' => 'required|json'
        ]);

        $train_numbers = json_decode($request->train_numbers);

        foreach ($train_numbers as $train_number) {
            if (Train::query()->where('numbers', $train_number)->exists()) {
                return response()->json([
                    'status_code' => 409,
                    'message' => '车次已存在'
                ])->setStatusCode(409);
            }
        }

        $train = Train::create([
            'numbers' => json_encode(array_unique($train_numbers)),
            'price'   => json_decode($request->prices)
        ]);
        
        $stations = json_decode($request->stations);

        foreach ($stations as $station) {
            if (!Station::find($station)->status) {
                return response()->json([
                    'status_code' => 404,
                    'message' => '车站不存在'
                ])->setStatusCode(404);
            }
        }

        $pivots = array();
        for ($i=0; $i < count($stations); $i++) { 
            $pivots[$i] = [
                'station_num' => $i + 1,
                'train_num'   => $train_numbers[$i],
                'arrive_time' => json_decode($request->times)[$i][0],
                'depart_time' => json_decode($request->times)[$i][1]
            ];
        }
        $stations_pivots = array_combine($stations, $pivots);

        $train->stations()->attach($stations_pivots);
        
        return new TrainResource($train);
    }

    public function update(Request $request, Train $train) {
        if ($request->user()->type != 'admin') {
            return response()->json([
                'status_code' => 403,
                'message' => '您无权进行此操作'
            ])->setStatusCode(403);
        }

        $this->validate($request, [
            'stations' => 'required|string',
            'train_numbers' => 'required|string',
            'times' => 'required|string',
            'prices' => 'required|json'
        ]);

        $train_numbers = json_decode($request->train_numbers);

        $new_train = Train::create([
            'numbers' => json_encode(array_unique($train_numbers)),
            'price'   => stripslashes(json_encode(json_decode($request->prices)))
        ]);

        $stations = json_decode($request->stations);

        foreach ($stations as $station) {
            if (!Station::find($station)->status) {
                return response()->json([
                    'status_code' => 404,
                    'message' => '车站不存在'
                ])->setStatusCode(404);
            }
        }

        $pivots = array();
        for ($i=0; $i < count($stations); $i++) { 
            $pivots[$i] = [
                'station_num' => $i + 1,
                'train_num'   => $train_numbers[$i],
                'arrive_time' => json_decode($request->times)[$i][0],
                'depart_time' => json_decode($request->times)[$i][1]
            ];
        }
        $stations_pivots = array_combine($stations, $pivots);

        $new_train->stations()->attach($stations_pivots);

        $train->status = false;
        $train->save();

        return (new TrainResource($new_train))->response()->setStatusCode(200);
    }

    public function delete(Request $request, Train $train) {
        if ($request->user()->type != 'admin') {
            return response()->json([
                'status_code' => 403,
                'message' => '您无权进行此操作'
            ])->setStatusCode(403);
        }

        $train->status = false;
        $train->save();

        return response()->json([
            'status_code' => 200,
            'message' => '删除成功'
        ])->setStatusCode(200);
    }
}
