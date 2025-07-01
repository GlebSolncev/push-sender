<?php

namespace App\Models\Statistic;

use App\Enums\CountriesEnum;
use App\Enums\PlatformsEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class View extends Model
{
    protected $table = 'statistic_views';
    protected $fillable = [
        'message_id',
        'ip',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
