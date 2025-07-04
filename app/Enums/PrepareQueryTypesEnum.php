<?php

namespace App\Enums;

use App\Services\PushNotification\Query\RegularPrepareQuery;
use App\Services\PushNotification\Query\PreviewPrepareQuery;

enum PrepareQueryTypesEnum: int
{
    case PREVIEW = 1;
    case REGULAR = 2;


    public function getPrepare(): string
    {
        return match ($this) {
            self::PREVIEW => PreviewPrepareQuery::class,
            self::REGULAR => RegularPrepareQuery::class,
        };
    }

}
