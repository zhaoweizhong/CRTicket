<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Passenger;
use App\Http\Resources\PassengerResource;

class PassengersController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();
        $passengers = $user->passengers;

        return PassengerResource::collection($passengers);
    }

    public function show(Request $request, Passenger $passenger) {
        $user = $request->user();
        if ($user->id != $passenger->user_id) {
            return response()->json([
                'status_code' => 403,
                'message' => '您无权查看该乘客信息'
            ])->setStatusCode(403);
        }

        return new PassengerResource($passenger);
    }

    public function store(Request $request) {
        $user = $request->user();

        $this->validate($request, [
            'name' => 'required|string',
            'id_type' => 'required|string|in:china_id,hkmo_pass,tw_pass,passport',
            'id_num' => 'required|string',
            'gender' => 'required|string|in:male,female',
            'mobile' => 'required|string',
            'type' => 'required|string|in:adult,child,student,military'
        ]);

        if ($request->id_type == 'china_id' && !$this->is_chinaid($request->id_num)) {
            return response()->json([
                'status_code' => 422,
                'message' => '身份证号码不合法'
            ])->setStatusCode(422);
        }

        $attributes = $request->only(['name', 'id_type', 'id_num', 'gender', 'mobile', 'type']);
        $attributes['user_id'] = $user->id;

        $passenger = Passenger::create($attributes);

        return new PassengerResource($passenger);
    }
    
    public function update(Request $request, Passenger $passenger) {
        $user = $request->user();
        if ($user->id != $passenger->user_id) {
            return response()->json([
                'status_code' => 403,
                'message' => '您无权修改该乘客信息'
            ])->setStatusCode(403);
        }

        $this->validate($request, [
            'name' => 'string',
            'id_type' => 'string|in:china_id,hkmo_pass,tw_pass,passport',
            'id_num' => 'string|required_with:id_type',
            'gender' => 'string|in:male,female',
            'mobile' => 'string',
            'type' => 'string|in:adult,child,student,military'
        ]);

        if ($request->has('id_num') && ($request->id_type == 'china_id' || (!$request->has('id_type') && $passenger->id_type == 'china_id')) && !$this->is_chinaid($request->id_num)) {
            return response()->json([
                'status_code' => 422,
                'message' => '身份证号码不合法'
            ])->setStatusCode(422);
        }

        $attributes = $request->only(['name', 'id_type', 'id_num', 'gender', 'mobile', 'type']);

        $passenger->update($attributes);

        return new PassengerResource($passenger);
    }

    public function delete(Request $request, Passenger $passenger) {
        $user = $request->user();
        if ($user->id != $passenger->user_id) {
            return response()->json([
                'status_code' => 403,
                'message' => '您无权删除该乘客信息'
            ])->setStatusCode(403);
        }

        if ($passenger->is_self) {
            return response()->json([
                'status_code' => 403,
                'message' => '您不能删除自己的信息'
            ])->setStatusCode(403);
        }

        $passenger->delete();

        return response()->json([
            'status_code' => 200,
            'message' => '删除成功'
        ])->setStatusCode(200);
    }

    public function is_chinaid($id)
    {
        $id = strtoupper($id);
        $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = array();
        if(!preg_match($regx, $id))
        {
            return FALSE;
        }
        if(15==strlen($id)) //检查15位
        {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
            @preg_match($regx, $id, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19".$arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
            if(!strtotime($dtm_birth))
            {
                return FALSE;
            }
            else
            {
                return TRUE;
            }
        }
        else //检查18位
        {
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            @preg_match($regx, $id, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
            if(!strtotime($dtm_birth)) //检查生日日期是否正确
            {
                return FALSE;
            }
            else
            {
                //检验18位身份证的校验码是否正确。
                //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
                $sign = 0;
                for ( $i = 0; $i < 17; $i++ )
                {
                    $b = (int) $id{$i};
                    $w = $arr_int[$i];
                    $sign += $b * $w;
                }
                $n = $sign % 11;
                $val_num = $arr_ch[$n];
                if ($val_num != substr($id,17, 1))
                {
                    return FALSE;
                }
                else
                {
                    return TRUE;
                }
            }
        }
    }
}
