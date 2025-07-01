<?php

namespace App\Http\Controllers;

use App\Models\Statistic\View;
use App\Services\TelegramSendMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StatisticController extends Controller
{
    public function lead(Request $request): JsonResponse
    {
        Log::log('debug', time() . ' |-| ' . json_encode($request->all()));

        return response()->json([
            'status' => true
        ]);
    }

    public function sub(Request $request, TelegramSendMessage $telegramMessage): bool
    {
        try {
            $data = $request->all();

            View::query()->insert([
                'message_id' => $data['msg_id'],
                'ip' => $data['ip'],
                'data' => json_encode($data)
            ]);

            $telegramMessage->handle('Open page, info: '. json_encode($request->all()));
        }catch (\Exception $exception){
            $json = json_encode($request->all());
            Log::error($exception->getMessage() . ' -=-' . $json);
            $telegramMessage->handle('Error open page' . $json . ' | ' . $exception->getMessage());

            return false;
        }

        return true;
    }
}