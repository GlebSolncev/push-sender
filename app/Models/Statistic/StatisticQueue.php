<?php

namespace App\Models\Statistic;

use App\Enums\StatisticQueueStatusEnum;
use Illuminate\Database\Eloquent\Model;

class StatisticQueue extends Model
{
    protected $table = 'statistic_queue';

    protected $fillable = [
        'message_id',
        'status',
        'success',
        'failed',
        'total',
    ];

    protected $primaryKey = 'message_id';

    protected $casts = [
        'status' => StatisticQueueStatusEnum::class,
    ];
}
