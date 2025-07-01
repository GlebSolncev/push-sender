<?php

namespace App\Http\Controllers;

use App\Http\Requests\PushSubscribeRequest;
use App\Models\PushSubscription;

class PushController extends Controller
{
    const ENCODE = 'aes128gcm';
    public function subscribe(PushSubscribeRequest $request) {
        $data = $request->validated();

        PushSubscription::query()->insertOrIgnore([
            'endpoint' => $data['endpoint'],
            'p256dh' => $data['publicKey'],
            'auth' => $data['authToken'],
        ]);

        return response()->json(['status' => true]);
    }
}
