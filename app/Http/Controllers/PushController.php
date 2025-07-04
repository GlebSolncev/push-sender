<?php

namespace App\Http\Controllers;

use App\Http\Requests\PushSubscribeRequest;
use App\Models\PushSubscription;

class PushController extends Controller
{
    public function subscribe(PushSubscribeRequest $request) {
        $data = $request->validated();

        PushSubscription::query()->insertOrIgnore([
            'endpoint' => $data['endpoint'],
            'public_key' => $data['publicKey'],
            'auth_token' => $data['authToken'],
        ]);

        return response()->json(['status' => true]);
    }
}
