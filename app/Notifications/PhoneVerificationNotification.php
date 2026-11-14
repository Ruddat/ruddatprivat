<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PhoneVerificationNotification extends Notification
{
    use Queueable;

    protected $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function via($notifiable)
    {
        // Wir nutzen hier kein Channel-System, sondern rufen API direkt
        return ['custom_sms'];
    }

    public function toArray($notifiable)
    {
        return [
            'code' => $this->code,
        ];
    }

    public function sendSms($phone)
    {
        return Http::withHeaders([
            // config() statt env(): env() liefert bei gecachter Konfiguration null
            'X-Api-Key' => config('services.seven.api_key'),
        ])->post(config('services.seven.endpoint'), [
            'to'   => $phone,
            'text' => "Dein RuddatTech Code lautet: {$this->code}",
            'from' => 'RuddatTech',
        ]);
    }
}