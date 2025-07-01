<?php

namespace App\Jobs;

use App\Logging\PushLogger;
use App\Models\Message;
use App\Models\PushSubscription;
use App\Models\Subscriber;
use App\Services\TelegramSendMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class SendPushNotification implements ShouldQueue
{
    use Queueable;

    const ENCODE = 'aes128gcm';

    protected int $countSuccess = 0;
    protected int $countTotal = 0;
    protected int $countFailed = 0;

    protected TelegramSendMessage $telegramSendMessage;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Message $message,
    ) {
        $this->telegramSendMessage = App::make(TelegramSendMessage::class);
    }

    /**
     * Execute the job.
     * @throws \ErrorException|\JsonException
     */
    public function handle()
    {
        $logger = new PushLogger();
//        $query = PushSubscription::query(); For TESTING
        $query = Subscriber::query()
            ->select(['id', 'endpoint', 'public_key', 'auth_token'])
            ->where('country', $this->message->country->value)
            ->orWhere('geo', $this->message->country->value);

        $this->countTotal = $query->count();

        $this->telegramSendMessage->handle(<<<HTML
<b>Start Push sanding.</b>
Message: <b>{$this->message->title}</b>
Count subscribers: <b>{$this->countTotal}</b>

<a href="https://pushification.online/admin/resource/message-resource/form-page/{$this->message->id}">Form page</a>
HTML
        );

        $webPush = new WebPush([
            'VAPID' => [
                'subject'    => 'mailto:admin',
                'publicKey'  => config('vapid.public_key'),
                'privateKey' => config('vapid.private_key'),
            ],
        ]);


        $counter = 0;
        foreach ($query->cursor() as $subscriber) {
            $counter++;

//            $this->send($subscriber, $webPush);
            $subscription = Subscription::create([
                'endpoint' => $subscriber->endpoint,
                "keys"     => [
                    'p256dh' => $subscriber->public_key,
                    'auth'   => $subscriber->auth_token
                ],
//            'publicKey'       => $subscriber->public_key,
//            'authToken'       => $subscriber->auth_token,
//            'contentEncoding' => self::ENCODE,
            ]);

            if ($this->message->icon)
                $icon = Storage::disk('public')->url($this->message->icon);
            if ($this->message->image)
                $image = Storage::disk('public')->url($this->message->image);

//        return $webPush->sendOneNotification(
            $webPush->queueNotification(
                $subscription,
                json_encode([
                    'title' => $this->message->title,
                    'icon'  => $icon,
                    'image' => $image,
                    'body'  => $this->message->body,
                    'data'  => [
                        'url' => $this->message->link . '?id=' . $subscriber->id . '&msg_id=' . $this->message->id,
                        'id'  => 1,
                    ],
                ], JSON_THROW_ON_ERROR)
            );
        }

        foreach ($webPush->flush() as $report) {
            $report->getRequest()->getUri()->__toString();

            if ($report->isSuccess()) {
                $this->countSuccess++;
            } else {
                $this->countFailed++;
            }

            $logger->log($subscriber->id . ' - ' . $report->getReason(), ['file' => 'message-' . $this->message->id]);

            if ($counter % 400 === 0) {
                $this->telegramSendMessage->handle(<<<HTML
                    <b>Status pack</b>
                    Message: <b>{$this->message->title}</b>
                    Success: <b>{$this->countSuccess}</b>
                    Failes: <b>{$this->countFailed}</b>
                    Total: <b>{$this->countTotal}</b>
                HTML
                );
            }

        }

        $this->telegramSendMessage->handle(<<<HTML
            <b>Finish sent</b>
            Message: <b>{$this->message->title}</b>
            Success: <b>{$this->countSuccess}</b>
            Failes: <b>{$this->countFailed}</b>
            Total: <b>{$this->countTotal}</b>
        HTML
        );

        return false;
    }

    protected function send($subscriber, $webPush): MessageSentReport
    {
        $subscription = Subscription::create([
            'endpoint' => $subscriber->endpoint,
            "keys"     => [
                'p256dh' => $subscriber->public_key,
                'auth'   => $subscriber->auth_token
            ],
//            'publicKey'       => $subscriber->public_key,
//            'authToken'       => $subscriber->auth_token,
//            'contentEncoding' => self::ENCODE,
        ]);

        if ($this->message->icon)
            $icon = Storage::disk('public')->url($this->message->icon);
        if ($this->message->image)
            $image = Storage::disk('public')->url($this->message->image);

//        return $webPush->sendOneNotification(
       return $webPush->queueNotification(
            $subscription,
            json_encode([
                'title' => $this->message->title,
                'icon'  => $icon,
                'image' => $image,
                'body'  => $this->message->body,
                'data'  => [
                    'url' => $this->message->link . '?id=' . $subscriber->id . '&msg_id=' . $this->message->id,
                    'id'  => 1,
                ],
            ], JSON_THROW_ON_ERROR)
        );
    }
}
