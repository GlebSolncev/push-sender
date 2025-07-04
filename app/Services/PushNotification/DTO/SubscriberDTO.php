<?php

namespace App\Services\PushNotification\DTO;

use Illuminate\Contracts\Support\Arrayable;

readonly class SubscriberDTO implements Arrayable
{

    public function __construct(
        protected string $endpoint,
        protected string $publicKey,
        protected string $authToken,
    ) {}

    public function toArray(): array
    {
        return [
            'endpoint' => $this->endpoint,
            "keys"     => [
                'p256dh' => $this->publicKey,
                'auth'   => $this->authToken
            ],
        ];
    }
}