<?php

namespace App\Jobs;

use App\Logging\PushLogger;
use App\Models\Message;
use App\Models\Subscriber;
use App\Services\TelegramSendMessage;
use Generator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class SendPushNotification implements ShouldQueue
{
    use Queueable;

    const ENCODE = 'aes128gcm';

    const LIMIT = 500;

    protected int $countSuccess = 0;
    protected int $countTotal = 0;
    protected int $countFailed = 0;

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
    public function handle(TelegramSendMessage $telegramSendMessage)
    {
        $telegramSendMessage->handle('Start Push sanding. Message: ' . $this->message->title . '\n https://pushification.online/admin/resource/message-resource/form-page/' . $this->message->id);

        $logger = new PushLogger();

        $query = Subscriber::query()
            ->select(['id', 'endpoint', 'public_key', 'auth_token'])
            ->where('country', $this->message->country->value)
            ->orWhere('geo', $this->message->country->value);

        $this->countTotal = $query->count();
        $telegramSendMessage->handle('Count subscribers: ' . $this->countTotal);

        $query
            ->orderBy('id')
            ->limit(self::LIMIT);

        $webPush = new WebPush([
            'VAPID' => [
                'subject'    => 'mailto:admin',
                'publicKey'  => config('vapid.public_key'),
                'privateKey' => config('vapid.private_key'),
            ],
        ]);


        foreach ($this->fetchSubscribers($telegramSendMessage) as $subscriber) {
            $subscription = Subscription::create([
                'endpoint'        => $subscriber->endpoint,
                'publicKey'       => $subscriber->public_key,
                'authToken'       => $subscriber->auth_token,
                'contentEncoding' => self::ENCODE,
            ]);

            if ($this->message->icon)
                $icon = Storage::disk('public')->url($this->message->icon);
            if ($this->message->image)
                $image = Storage::disk('public')->url($this->message->image);

            $report = $webPush->sendOneNotification(
                $subscription,
                json_encode([
                    'title' => $this->message->title,
                    'icon'  => $icon,
                    'image' => $image,
                    'body'  => $this->message->body,
                    'data'  => [
                        'url' => $this->message->link . '?id=' . $subscriber->id . '&msg_id=' . $this->message->id, // need id=subId&msg_id=MessageID
                        'id'  => 1,
                    ],
                ], JSON_THROW_ON_ERROR)
            );

            if($report->isSuccess()){
                $this->countSuccess++;
            }else{
                $this->countFailed++;
            }

            $logger->log($subscriber->id . ' - ' . $report->getReason(), ['file' => 'message-' . $this->message->id]);
        }

        $telegramSendMessage->handle('Finish for message: ' . $this->message->title . ' Success: ' . $this->countSuccess . ' Failed: ' . $this->countFailed);

        return false;
    }


    public function fetchSubscribers(TelegramSendMessage $telegramSendMessage): Generator
    {
        $lastId = 0;
        do {
            $rows = Subscriber::query()
                ->select(['id', 'endpoint', 'public_key', 'auth_token'])
                ->where('country', $this->message->country->value)
//                ->orWhere('geo', $this->message->country->value)
                ->where('id', '>', $lastId)
                ->orderBy('id')
                ->limit(self::LIMIT)
                ->get();

            $lastId = $rows->max('id');

            foreach ($rows as $row) {
                yield $row;
            }

            $telegramSendMessage->handle('Success: ' . $this->countSuccess . ' Failed: ' . $this->countFailed . ' Total: ' . $this->countTotal);
        } while ($rows->isNotEmpty());
    }
}
