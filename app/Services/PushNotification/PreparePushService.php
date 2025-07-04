<?php

namespace App\Services\PushNotification;

use App\Enums\PrepareQueryTypesEnum;
use App\Jobs\SenderPushNotificationJob;
use App\Logging\PushLogger;
use App\Models\Message;
use App\Models\Statistic\StatisticQueue;
use App\Services\PushNotification\DTO\PayloadPrepareDTO;
use App\Services\PushNotification\DTO\SubscriberDTO;
use App\Services\PushNotification\Query\PrepareQueryInterface;
use App\Services\PushNotification\ValueObjects\SubscriberLink;
use App\Services\TelegramSendMessage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Minishlink\WebPush\Subscription;

class PreparePushService
{
    const CHUNK = 1000;

    public function __construct(
        private readonly TelegramSendMessage $telegramSendMessage,
        private readonly PushLogger $logger,
    ) {}

    public function handle(PrepareQueryTypesEnum $typeEnum, Message $message): void
    {
        $prepare = $this->getPrepareQuery($typeEnum);
        $query = $prepare->handle($message);
        $total = $query->count();
        $this->updateStatisticQueue($message->id, $total);
        $this->telegramMessage($message, $total);
        $this->logger->log('Start. Total: ' . $total, $message->id);

        $counter = 0;
        $step = 0;
        $allSteps = floor($total / self::CHUNK);
        foreach ($query->cursor() as $subscriber) {
            $counter++;
            $link = $this->getLink($message, $subscriber);
            $subscriptions[$link] = $this->getSubscription($subscriber);;

            if ($counter % self::CHUNK === 0) {
                $step++;
                SenderPushNotificationJob::dispatch(
                    $message->id,
                    $subscriptions,
                    $this->getPayload($message),
                    $this->getStepInfo($step, $allSteps),
                )->onQueue('send-push-notification');
                $subscriptions = [];
            }
        }

        if (empty($subscriptions) === false) {
            SenderPushNotificationJob::dispatch(
                $message->id,
                $subscriptions,
                $this->getPayload($message),
                $this->getStepInfo($step+1, $allSteps+1),
            )->onQueue('send-push-notification');
        }
    }

    private function getStepInfo(int $currentStep,int $allSteps){
        return $currentStep . '/' . $allSteps . '(by ' . self::CHUNK . ')';
    }

    protected function getPayload(Message $message): array
    {
        $dto = new PayloadPrepareDTO(
            $message->title,
            $message->body,
            [
                'url' => $message->link,
                'id'  => 1,
            ]
        );

        $dto->setIcon($message->icon);
        $dto->setImage($message->image);

        return $dto->toArray();
    }

    protected function getSubscription(Model $subscriber): Subscription
    {
        $subscriber = new SubscriberDTO($subscriber->endpoint, $subscriber->public_key, $subscriber->auth_token);

        return Subscription::create($subscriber->toArray());
    }

    protected function getLink(Message $message, Model $subscriber): string
    {
        $subscriberLink = new SubscriberLink($message->link);
        $subscriberLink->withParams($subscriber->id, $message->id);

        return $subscriberLink->toString();
    }

    protected function getPrepareQuery(PrepareQueryTypesEnum $typeEnum): PrepareQueryInterface
    {
        return App::make($typeEnum->getPrepare());
    }

    private function telegramMessage(Model $message, int $count){
        $this->telegramSendMessage->handle(
            <<<HTML
                <b>Prepare send push notification</b>
                Message: <b>$message->title</b>
                Total: <b>$count</b>
            HTML
        );
    }

    private function updateStatisticQueue(int $messageId, int $total)
    {
        StatisticQueue::query()->where('message_Id', $messageId)
            ->update([
                'total' => $total,
                'success' => 0,
                'failed' => 0,
            ]);
    }

}