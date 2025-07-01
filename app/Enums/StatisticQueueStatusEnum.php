<?php

namespace App\Enums;

enum StatisticQueueStatusEnum: int
{
    case START = 0;

    case IN_PROCESS = 1;

    case FAILED = 2;

}

