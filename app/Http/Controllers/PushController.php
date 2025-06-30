<?php

namespace App\Http\Controllers;

use App\Http\Requests\PushSubscribeRequest;
use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

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

    /**
     * @throws \JsonException
     * @throws \ErrorException
     */
    public function test() {
        $subs = PushSubscription::query()->get();

        foreach($subs as $sub) {

            $subscription = Subscription::create([
                'endpoint'        => $sub->endpoint,
                'publicKey'       => $sub->p256dh,
                'authToken'       => $sub->auth,
                'contentEncoding' => self::ENCODE,
            ]);


            $webPush = new WebPush([
                'VAPID' => [
                    'subject'    => 'mailto:admin',
                    'publicKey'  => config('vapid.public_key'),
                    'privateKey' => config('vapid.private_key'),
                ],
            ]);

            $report = $webPush->sendOneNotification(
                $subscription,
                json_encode([
                    'id'    => 1,
                    'title' => 'test',
                    'image' => null,
                    'body'  => 'test',
                ], JSON_THROW_ON_ERROR)
            );

        }

        $reason = $report->getReason();

        return $reason;
    }


    public function statistic(Request $request) {
        dd(
            $request->all()
        );
    }
}
