<?php

namespace App\Services\PushNotification\Query;

use App\Models\Message;
use App\Models\PushSubscription;
use Illuminate\Database\Eloquent\Builder;

class PreviewPrepareQuery implements PrepareQueryInterface
{

    public function handle(Message $message): Builder
    {
        return PushSubscription::query();
    }
}