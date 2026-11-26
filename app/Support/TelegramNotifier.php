<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifier
{
    /**
     * Send a plain-text notification to the configured shop admin Telegram chat.
     */
    public static function sendText(string $text): bool
    {
        try {
            $token = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
            $chatId = config('services.telegram.chat_id') ?: env('TELEGRAM_CHAT_ID');
            if (empty($token) || empty($chatId)) {
                return false;
            }

            $response = Http::timeout(4)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Telegram notify error: ' . $e->getMessage());
            return false;
        }
    }
}
