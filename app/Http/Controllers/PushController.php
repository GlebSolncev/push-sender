<?php

namespace App\Http\Controllers;

use App\Http\Requests\PushSubscribeRequest;
use App\Models\Message;
use App\Models\PushSubscription;
use App\Services\TestPushNotification;
use Illuminate\Http\Request;

class PushController extends Controller
{
    const ENCODE = 'aes128gcm';
    public function subscribe(PushSubscribeRequest $request) {
        $data = $request->validated();

        PushSubscription::query()->insertOrIgnore([
            'endpoint' => $data['endpoint'],
            'public_key' => $data['publicKey'],
            'auth_token' => $data['authToken'],
        ]);

        return response()->json(['status' => true]);
    }

    public function test(int $id, TestPushNotification $notification)
    {
        [$total, $success, $fail] = $notification->send(
            Message::query()->find($id)
        );

        return 'Total: '. $total . ' Success: ' . $success . ' Failed: ' . $fail;
    }
}
