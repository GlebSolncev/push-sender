<?php

namespace App\Models;

use App\Enums\CountriesEnum;
use App\Enums\PlatformsEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Message extends Model
{
    protected $fillable = [
        'title',
        'icon',
        'image',
        'body',
        'link',
        'platform',
        'country',
    ];


    protected $casts = [
        'platform' => PlatformsEnum::class,
        'country'  => CountriesEnum::class,
//        'icon_path'     => S3FileCast::class,
//        'image_path'    => S3FileCast::class,
    ];

    public function countSubscribers(): int
    {
        $item = DB::table('subscribers')
            ->select(DB::raw('count(*) as count'))
            ->where([
                ['country', '=', $this->country->value],
                ['platform', '=', $this->platform->value]
            ])
            ->first();

        if ($item->count ?? null) {
            return $item->count;
        }

        return 0;
    }
}
