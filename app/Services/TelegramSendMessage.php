<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;

class TelegramSendMessage
{
    const CHAT_ID = '377309632';

    public function __construct(
        protected Nutgram $nutgram
    ) {}

    public function handle(string $message): bool
    {
        try {
            $this->nutgram->sendMessage(
                text: $message,
                chat_id: self::CHAT_ID,
                parse_mode: 'HTML'
            );
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            return false;
        }


        return true;
    }
}

