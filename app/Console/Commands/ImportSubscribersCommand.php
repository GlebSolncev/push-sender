<?php

namespace App\Console\Commands;

use App\Models\PushSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class ImportSubscribersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:subscribers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = Storage::disk('public')->path('data.csv');
        if(!file_exists($path)) throw new \Exception('not found file');

        $f = fopen($path, 'r');
        $keys = fgetcsv($f);          // первая строка — заголовки
        $data = [];

        while (($row = fgetcsv($f)) !== false) {
            $data[] = array_combine($keys, $row);
        }

        fclose($f);

        $item = $data[random_int(0, count($data)-1)];


        $subscription = Subscription::create([
            'endpoint' => $item['endpoint'],
            'publicKey' => $item['public_key'],
            'authToken' => $item['auth_token'],
            'contentEncoding' => 'aes128gcm',
        ]);


        $webPush = new WebPush([
            'VAPID' => [
                'subject' => 'mailto:admin',
                'publicKey' => config('vapid.public_key'),
                'privateKey' => config('vapid.private_key'),
            ],
        ]);

        $report = $webPush->sendOneNotification(
            $subscription,
            json_encode([
                'title' => 'test',
                'body'  => 'test',
            ], JSON_THROW_ON_ERROR)
        );


        dd(
            $report
        );
    }
}
