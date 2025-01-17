<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Services\TelegramService;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

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
