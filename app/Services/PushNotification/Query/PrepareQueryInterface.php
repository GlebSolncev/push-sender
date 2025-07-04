<?php

namespace App\Services\PushNotification\Query;

use App\Models\Message;
use Illuminate\Database\Eloquent\Builder;

interface PrepareQueryInterface
{
    public function handle(Message $message): Builder;
}