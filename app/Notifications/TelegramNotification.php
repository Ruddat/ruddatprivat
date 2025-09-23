<?php

namespace App\Notifications;

use App\Services\TelegramService;
use Illuminate\Notifications\Notification;

class TelegramNotification extends Notification
{
    protected $message;

    protected $chatId;

    public function __construct(string $message, string $chatId)
    {
        $this->message = $message;
        $this->chatId = $chatId;
    }

    public function via($notifiable)
    {
        return ['telegram'];
    }

    public function toTelegram()
    {
        $telegramService = app(TelegramService::class);
        $telegramService->sendMessage($this->chatId, $this->message);
    }
}
