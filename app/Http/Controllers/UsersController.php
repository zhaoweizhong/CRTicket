<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;

class UsersController extends Controller {
    public function me(Request $request) {
        return new UserResource($request->user());
    }

    public function show(Request $request, User $user) {
        if ($request->user()->type != 'admin') {
            return response()->json([
                'status_code' => 403,
                'message' => '您无权查看用户信息'
            ])->setStatusCode(403);
        }

        return new UserResource($user);
    }

    public function update(Request $request, User $user) {
        if ($request->user()->id != $user->id || $request->user()->type != 'admin') {
            return response()->json([
                'status_code' => 403,
                'message' => '您无权修改该用户信息'
            ])->setStatusCode(403);
        }

        $this->validate($request, [
            'password' => 'string',
        ]);

        $attributes['password'] = bcrypt($request->password);

        $user->update($attributes);

        return new UserResource($user);
    }
}
