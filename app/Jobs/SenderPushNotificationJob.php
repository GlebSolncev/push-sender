<?php

namespace App\Jobs;

use App\Models\Statistic\StatisticQueue;
use App\Services\TelegramSendMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\App;
use Minishlink\WebPush\WebPush;

class SenderPushNotificationJob implements ShouldQueue
{
    use Queueable;

    protected ?StatisticQueue $statisticQueue = null;

    protected TelegramSendMessage $telegramSendMessage;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $messageId,
        protected array $subscriptions,
        protected array $payload,
        protected int $step
    ) {
        $this->telegramSendMessage = App::make(TelegramSendMessage::class);

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->makeStatisticQueue();
        $webPush = $this->getWebPushInstance();
        $countSuccess = $this->statisticQueue->success;
        $countFail = $this->statisticQueue->failed;

        foreach ($this->subscriptions as $link => $subscription) {
            $this->payload['data']['url'] = $link;
            $webPush->queueNotification(
                $subscription,
                json_encode($this->payload, JSON_THROW_ON_ERROR)
            );
        }

        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                $countSuccess++;
            } else {
                $countFail++;
            }
        }

        $this->telegramSendMessage->handle(<<<HTML
            <b>Finish STEP {$this->step}</b>
            Success: <b>{$countSuccess}</b>
            Failes: <b>{$countFail}</b>
            Total: <b>{$this->statisticQueue->total}</b>
        HTML
        );

        $this->updateCounters($countSuccess, $countFail);
    }

    protected function getWebPushInstance(): WebPush
    {
        return App::make(WebPush::class, [
            'auth'    => [
                'VAPID' => [
                    'subject'    => 'mailto:admin',
                    'publicKey'  => config('vapid.public_key'),
                    'privateKey' => config('vapid.private_key'),
                ],
            ],
            'timeout' => 2
        ]);
    }
    protected function makeStatisticQueue(): void
    {
        $this->statisticQueue = StatisticQueue::query()->find($this->messageId);
    }

    protected function updateCounters(int $success, int $fail): void
    {
        $this->statisticQueue->success = $success;
        $this->statisticQueue->failed = $fail;

        $this->statisticQueue->save();
    }
}
