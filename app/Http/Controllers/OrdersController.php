<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Http\Resources\TicketResource;
use App\Models\Order;
use App\Models\Passenger;
use App\Models\Seat;
use App\Models\Station;
use App\Models\Ticket;
use App\Models\Train;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function store(Request $request, Order $order) {
        $this->validate($request, [
            'train_id' => 'required|integer',
            'date' => 'required|date',
            'depart_station' => 'required|integer',
            'arrive_station' => 'required|integer',
            'seat_type' => 'required|string|in:SZ,1Z,2Z,YZ,YW,RW',
            'passenger_id' => 'required|integer'
        ]);
        $user = $request->user();
        $train = Train::find($request->train_id);
        $passenger = Passenger::find($request->passenger_id);
        $date = $request->date;
        $type = $request->seat_type;
        $depart_station = Station::find($request->depart_station);
        $arrive_station = Station::find($request->arrive_station);
        $depart_station_num = $train->stations()->where('station_id', $depart_station->id)->first()->pivot->station_num;
        $arrive_station_num = $train->stations()->where('station_id', $arrive_station->id)->first()->pivot->station_num;

        if (!$train->status) {
            return response()->json([
                'status_code' => 404,
                'message' => '列车不存在'
            ])->setStatusCode(404);
        }

        if (Seat::getAvailable($train, $date, $type, $depart_station, $arrive_station) <= 0) {
            return response()->json([
                'status_code' => 400,
                'message' => '该席位余票不足'
            ])->setStatusCode(400);
        }

        $result = Seat::getAvailableSeatNumber($train, $depart_station_num, $arrive_station_num, $date, $type);
        $carriage = $result[0];
        $seat = $result[1];

        $order = Order::create([
            'user_id' => $user->id,
            'status'  => 'unpaid'
        ]);

        $ticket = Ticket::create([
            'order_id'           => $order->id,
            'train_id'           => $train->id,
            'date'               => $date,
            'depart_station_id'  => $depart_station->id,
            'arrive_station_id'  => $arrive_station->id,
            'depart_station_num' => $depart_station_num,
            'arrive_station_num' => $arrive_station_num,
            'passenger_id'       => $passenger->id,
            'seat_type'          => $type,
            'carriage'           => $carriage,
            'seat'               => $seat,
            'price'              => json_decode($train->price)[$depart_station_num][$arrive_station_num][$type]
        ]);

        return [
            'order'  => new OrderResource($order),
            'ticket' => new TicketResource($ticket)
        ];
    }
    
    public function pay(Request $request, Order $order) {
        $user = $request->user();
        if ($user->id != $order->user_id) {
            return response()->json([
                'status_code' => 403,
                'message' => '您无权进行此操作'
            ])->setStatusCode(403);
        }

        $order->status = 'paid';
        $order->save();

        return new OrderResource($order);
    }

    public function cancel(Request $request, Order $order) {
        $user = $request->user();
        if ($user->id != $order->user_id && $user->type != 'admin') {
            return response()->json([
                'status_code' => 403,
                'message' => '您无权进行此操作'
            ])->setStatusCode(403);
        }

        $tickets = $order->tickets;
        foreach ($tickets as $ticket) {
            $train = Train::find($ticket->train_id);
            $date = $ticket->date;
            $depart_station_num = $ticket->depart_station_num;
            $arrive_station_num = $ticket->arrive_station_num;
            
            $demand_sets_value = 0;
            for ($i=$depart_station_num; $i < $arrive_station_num; $i++) { 
                $demand_sets_value = $demand_sets_value + pow(2, $i);
            }

            $seat = Seat::where('train_id', $train->id)->whereDate('date', $date)->first();
            $seat_status = $seat->status;
            $seat_status_value = bindec($seat_status);

            $seat->status = $seat_status_value - $demand_sets_value;
            $seat->save();

            $ticket->delete();
        }

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'status_code' => 200,
            'message' => '取消成功'
        ])->setStatusCode(200);
    }
}
