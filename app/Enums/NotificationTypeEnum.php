<?php

namespace App\Enums;

enum NotificationTypeEnum: int
{
    case START = 0;

    case SEND = 1;

    case SHOW = 2;
    case CLICK = 3;

    case CLOSE = 4;

    case FAILED = 10;


    public function label(): string
    {
        return match ($this) {
            self::START => 'start',
            self::FAILED => 'failed',
            self::SEND => 'send',
            self::SHOW => 'show',
            self::CLICK => 'click',
            self::CLOSE => 'close',
        };
    }
}

