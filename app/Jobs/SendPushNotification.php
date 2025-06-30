<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\PushSubscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class SendPushNotification implements ShouldQueue
{
    use Queueable;

    const ENCODE = 'aes128gcm';

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Message $message,
    ) {}

    /**
     * Execute the job.
     * @throws \ErrorException|\JsonException
     */
    public function handle(): void
    {
        $subscribers = PushSubscription::query()->get();

        foreach($subscribers as $inx => $subscriber) {
            $subscription = Subscription::create([
                'endpoint' => $subscriber->endpoint,
                'publicKey' => $subscriber->p256dh,
                'authToken' => $subscriber->auth,
                'contentEncoding' => self::ENCODE,
            ]);


            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => 'mailto:admin',
                    'publicKey' => config('vapid.public_key'),
                    'privateKey' => config('vapid.private_key'),
                ],
            ]);

            $image = null;
            $icon = null;

            if($this->message->icon) {
                $icon = Storage::disk('s3')->url($this->message->icon);
            }

            if(!$icon) {
                if($this->message->image) {
                    $image = Storage::disk('s3')->url($this->message->image);
                }
            }


            $report = $webPush->sendOneNotification(
                $subscription,
                json_encode([
                    'title' => $this->message->title . ' - ' . $inx,
                    'icon'  => $icon,
                    'image' => $image,
                    'body'  => $this->message->body,
                    'data'  => [
                        'url' => $this->message->link,
                        'id'  => 1,
                    ],
                ], JSON_THROW_ON_ERROR)
            );
        }


        $reason = $report->getReason();
    }
}
