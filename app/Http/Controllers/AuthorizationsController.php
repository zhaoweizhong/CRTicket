<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthorizationsController extends Controller {
    public function store(Request $request) {
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials['username'] = $request->username;
        $credentials['password'] = $request->password;

        if (!$token = \Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'status_code' => 401,
                'message' => '用户名或密码错误'
            ])->setStatusCode(401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
        ])->setStatusCode(201);
    }

    public function update() {
        $token = auth('api')->refresh();
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }

    public function destroy() {
        auth('api')->logout();
        return response(null, 204);
    }
}
