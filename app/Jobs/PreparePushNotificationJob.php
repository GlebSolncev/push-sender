<?php

namespace App\Jobs;

use App\Enums\StatisticQueueStatusEnum;
use App\Models\Message;
use App\Models\PushSubscription;
use App\Models\Statistic\StatisticQueue;
use App\Models\Subscriber;
use App\Services\TelegramSendMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PreparePushNotificationJob implements ShouldQueue
{
    use Queueable;

    protected TelegramSendMessage $telegramSendMessage;

    const LIMIT_PART = 500;


    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Message $message
    ) {
        $this->telegramSendMessage = App::make(TelegramSendMessage::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = $this->getQuery();
        $countTotal = $query->count();
        $this->resetStatisticQueue($countTotal);
        $this->sendMessageTelegram("Count subscribers: <b>{$countTotal}</b>");

        $counter = 0;
        $subscriptions = [];
        foreach ($query->cursor() as $inx =>  $subscriber) {
            $counter++;
            $key = $this->message->link . '?id=' . $subscriber->id . '&msg_id=' . $this->message->id;
            $subscriptions[$key] = $this->getSubscription($subscriber->endpoint, $subscriber->public_key, $subscriber->auth_token);

            if ($counter % self::LIMIT_PART === 0) {
                $key = $this->message->link . '?id=XXXX&msg_id=' . $this->message->id;
                $subscriptions[$key] = $this->getSubscriptionForCheck();

                SenderPushNotificationJob::dispatch(
                    $this->message->id,
                    $subscriptions,
                    $this->getPayload(),
                    $inx + 1
                )->onQueue('send-push-notification');
                $subscriptions = [];
            }
        }

        if ($counter % self::LIMIT_PART !== 0) {
            $key = $this->message->link . '?id=XXXX&msg_id=' . $this->message->id;
            $subscriptions[$key] = $this->getSubscriptionForCheck();

            SenderPushNotificationJob::dispatch(
                $this->message->id,
                $subscriptions,
                $this->getPayload(),
                $inx + 1
            )->onQueue('send-push-notification');
        }
    }

    protected function getSubscriptionForCheck(): Subscription
    {
        $s = PushSubscription::query()->first();

        return Subscription::create([
            'endpoint' => $s->endpoint,
            "keys"     => [
                'p256dh' => $s->public_key,
                'auth'   => $s->auth_token,
            ],
        ]);
    }

    protected function resetStatisticQueue(int $total): void
    {
        StatisticQueue::query()
            ->where('message_id', $this->message->id)
            ->update([
                'status'  => StatisticQueueStatusEnum::IN_PROCESS,
                'success' => 0,
                'failed'  => 0,
                'total'   => $total,
            ]);
    }

    protected function getPayload(): array
    {//Model $subscriber
        if ($this->message->icon) {
            $icon = Storage::disk('public')->url($this->message->icon);
        }

        if ($this->message->image) {
            $image = Storage::disk('public')->url($this->message->image);
        }

        return [
            'title' => $this->message->title,
            'icon'  => $icon,
            'image' => $image,
            'body'  => $this->message->body,
            'data'  => [
                'url' => $this->message->link,
                'id'  => 1,
            ],
        ];
    }

    protected function getSubscription(string $endpoint, string $publicKey, string $authToken): Subscription
    {
        return Subscription::create([
            'endpoint' => $endpoint,
            "keys"     => [
                'p256dh' => $publicKey,
                'auth'   => $authToken
            ],
        ]);
    }

    protected function getQuery(): Builder
    {
        return Subscriber::query()
            ->select(['id', 'endpoint', 'public_key', 'auth_token'])
            ->where('country', $this->message->country->value)
            ->orWhere('geo', $this->message->country->value);
    }

    protected function sendMessageTelegram(string $body)
    {
        $this->telegramSendMessage->handle(<<<HTML
<b>Prepare Push sanding.</b>
Message: <b>{$this->message->title}</b>

{$body}

<a href="https://pushification.online/admin/resource/message-resource/form-page/{$this->message->id}">Form page</a>
HTML
        );
    }
}
