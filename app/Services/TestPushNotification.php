<?php

namespace App\Services;

use App\Models\Message;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Storage;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class TestPushNotification
{

    const ENCODE = 'aes128gcm';

    /**
     * @throws \ErrorException
     * @throws \JsonException
     */
    public function send(Message $message): array
    {
        $success = 0;
        $fail = 0;

        $query = PushSubscription::query();
        $total = $query->count();
        $webPush = new WebPush([
            'VAPID' => [
                'subject'    => 'mailto:admin',
                'publicKey'  => config('vapid.public_key'),
                'privateKey' => config('vapid.private_key'),
            ],
        ]);

        foreach ($query->cursor() as $subscriber) {
            $subscription = Subscription::create([
                'endpoint'        => $subscriber->endpoint,
                'publicKey'       => $subscriber->public_key,
                'authToken'       => $subscriber->auth_token,
                'contentEncoding' => self::ENCODE,
            ]);

            if ($message->icon)
                $icon = Storage::disk('public')->url($message->icon);
            if ($message->image)
                $image = Storage::disk('public')->url($message->image);

            $webPush->queueNotification(
                $subscription,
                json_encode([
                    'title' => $message->title,
                    'icon'  => $icon,
                    'image' => $image,
                    'body'  => $message->body,
                    'data'  => [
                        'url' => $message->link . '?id=' . $subscriber->id . '&msg_id=' . $message->id,
                        'id'  => 1,
                    ],
                ], JSON_THROW_ON_ERROR)
            );

//            $report = $webPush->sendOneNotification(
//                $subscription,
//                json_encode([
//                    'title' => $message->title,
//                    'icon'  => $icon,
//                    'image' => $image,
//                    'body'  => $message->body,
//                    'data'  => [
//                        'url' => $message->link . '?id=' . $subscriber->id . '&msg_id=' . $message->id,
//                        'id'  => 1,
//                    ],
//                ], JSON_THROW_ON_ERROR)
//            );

//            if($report->isSuccess()) $success++;
//            else $fail++;
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();

            if($report->isSuccess()) $success++;
            else $fail++;
        }

        return [$total, $success, $fail];

    }

}