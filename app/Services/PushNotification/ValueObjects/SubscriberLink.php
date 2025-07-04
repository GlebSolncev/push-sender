<?php

namespace App\Services\PushNotification\ValueObjects;

class SubscriberLink
{
    private array $params = [];

    public function __construct(
        protected string $link
    ) {}

    public function withParams(
        int $subscriberId,
        int $messageId,
    ) {
        $this->params = [
            'id' => $subscriberId,
            'msg_id' => $messageId,
        ];
    }

    public function toString(): string
    {
        return $this->link . '?' . http_build_query($this->params);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}