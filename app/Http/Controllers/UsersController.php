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
                'message' => '您无权查看'
            ])->setStatusCode(403);
        }

        return new UserResource($user);
    }
}
