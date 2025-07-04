<?php

namespace App\Services\PushNotification\Query;

use App\Models\Message;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Builder;

class RegularPrepareQuery implements PrepareQueryInterface
{


    public function handle(Message $message): Builder
    {
        $country = $message->country->value;

        return Subscriber::query()
            ->select(['id', 'endpoint', 'public_key', 'auth_token'])
            ->where('country', $country)
            ->orWhere('geo', $country);
    }
}