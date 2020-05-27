<?php

namespace App\Http\Controllers;

use App\Http\Resources\StationResource;
use App\Models\Station;
use Illuminate\Http\Request;

class StationsController extends Controller
{
    public function index(Request $request, Station $station) {
        $query = $station->query();
        
        $stations = $query->where('status', 1)->paginate();

        return StationResource::collection($stations);
    }

    public function search(Request $request, Station $station) {
        $query = $station->query();
        $content = $request->content;

        $query = $query->where('status', 1)->where('name', 'like', '%'.$content.'%')->orWhere('quanpin', 'like', '%'.$content.'%')->orWhere('jianpin', 'like', '%'.$content.'%');

        $query = $query->take(10)->get();

        return StationResource::collection($query);
    }

    public function show(Station $station) {
        return new StationResource($station);
    }

    public function store(Request $request) {
        if ($request->user()->type != 'admin') {
            return response()->json([
                'status_code' => 403,
                'message' => '您无权进行此操作'
            ])->setStatusCode(403);
        }

        $this->validate($request, [
            'name' => 'required|string',
            'city' => 'string'
        ]);

        $attributes = $request->only(['name', 'city']);

        $station = Station::create($attributes);

        return new StationResource($station);
    }

    public function update(Request $request, Station $station) {
        if ($request->user()->type != 'admin') {
            return response()->json([
                'status_code' => 403,
                'message' => '您无权进行此操作'
            ])->setStatusCode(403);
        }

        $this->validate($request, [
            'name' => 'string',
            'city' => 'string'
        ]);

        $attributes = $request->only(['name', 'city']);

        $station->update($attributes);

        return new StationResource($station);
    }

    public function delete(Request $request, Station $station) {
        if ($request->user()->type != 'admin') {
            return response()->json([
                'status_code' => 403,
                'message' => '您无权进行此操作'
            ])->setStatusCode(403);
        }

        $station->status = false;
        $station->save();

        return response()->json([
            'status_code' => 200,
            'message' => '删除成功'
        ])->setStatusCode(200);
    }
}
