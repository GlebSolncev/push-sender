<?php

namespace App\Jobs;

use App\Models\Statistic\StatisticQueue;
use App\Services\TelegramSendMessage;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\WebPush;

class SenderPushNotificationJob implements ShouldQueue
{
    use Queueable;

    protected TelegramSendMessage $telegramSendMessage;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $messageId,
        protected array $subscriptions,
        protected array $payload,
        protected string $step
    ) {
        $this->telegramSendMessage = App::make(TelegramSendMessage::class);
    }

    /**
     * Execute the job.
     * @throws \JsonException|\ErrorException
     */
    public function handle(): void
    {
        $webPush = $this->getWebPushInstance();
        $countSuccess = 0;
        $countFail = 0;

        foreach ($this->subscriptions as $link => $subscription) {
            $this->payload['data']['url'] = $link;

            $report = $webPush->sendOneNotification(
                $subscription,
                json_encode($this->payload, JSON_THROW_ON_ERROR)
            );

//            $webPush->queueNotification(
//                $subscription,
//                json_encode($this->payload, JSON_THROW_ON_ERROR)
//            );

            if ($report->isSuccess()) {
                $countSuccess++;
                Log::log('info', '[TRUE] ' . $link);
            } else {
                $countFail++;
                Log::log('info', '[FALSE] ' . $link);
            }
        }

        $this->updateCounters($countSuccess, $countFail);
    }

    protected function getWebPushInstance(): WebPush
    {
        return App::make(WebPush::class, [
            'auth'    => [
                'VAPID' => [
                    'subject'    => 'mailto:yamokasy2@gmail.com',
                    'publicKey'  => config('vapid.public_key'),
                    'privateKey' => config('vapid.private_key'),
                ],
            ],
            'timeout' => 0.5
        ]);
    }

    protected function updateCounters(int $success, int $fail): void
    {
        DB::beginTransaction();
        try {

            $row = StatisticQueue::where('message_id', $this->messageId)
                ->lockForUpdate()
                ->first();

            $row->success += $success;
            $row->failed += $fail;
            $row->save();
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
        }

        $this->telegramSendMessage->handle(<<<HTML
            <b>Finish STEP {$this->step}</b>
            Success: <b>{$row->success}</b>
            Failes: <b>{$row->failed}</b>
            Total: <b>{$row->total}</b>
        HTML
        );
    }
}
