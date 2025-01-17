<?php

namespace App\Services;

use Telegram\Bot\Api;

class TelegramService
{
    protected $telegram;

    public function __construct()
    {
        $this->telegram = new Api(config('services.telegram.bot_token'));
    }

    /**
     * Send a message to a specific Telegram chat ID.
     *
     * @param string $chatId
     * @param string $message
     * @return void
     */
    public function sendMessage(string $chatId, string $message): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
        ]);
    }
}
