<?php

namespace App\Livewire\Frontend\ContactForm;

use App\Models\ContactMessage;
use DefStudio\Telegraph\Models\TelegraphBot;
use DefStudio\Telegraph\Models\TelegraphChat;
use Livewire\Component;

class ContactFormComponent extends Component
{
    public $name;

    public $email;

    public $subject;

    public $message;

    public $dataConsent;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|max:1000',
        'dataConsent' => 'accepted',
    ];

    public function submit()
    {
        $this->validate();

        // dd($this->name, $this->email, $this->subject, $this->message, $this->dataConsent);

        // Speichern in der Datenbank
        $contactMessage = ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'dataConsent' => $this->dataConsent,
        ]);

        // Nachricht an Telegram senden
        $this->sendToTelegram($contactMessage);

        // Formular zurücksetzen und Erfolgsmeldung anzeigen
        $this->reset();
        session()->flash('success', 'Ihre Nachricht wurde erfolgreich gesendet.');
    }

    private function sendToTelegram(ContactMessage $contactMessage)
    {
        $bot = TelegraphBot::first(); // Ersetzen Sie dies durch Ihre Bot-Konfiguration
        $chat = TelegraphChat::first(); // Ersetzen Sie dies durch Ihre Chat-ID

        $telegramMessage = "Neue Kontaktanfrage:\n";
        $telegramMessage .= "Name: {$contactMessage->name}\n";
        $telegramMessage .= "E-Mail: {$contactMessage->email}\n";
        $telegramMessage .= "Betreff: {$contactMessage->subject}\n";
        $telegramMessage .= "Nachricht: {$contactMessage->message}";

        if ($chat) {
            $chat->message($telegramMessage)->send();
        }
    }

    public function render()
    {
        return view('livewire.frontend.contact-form.contact-form-component');
    }
}
