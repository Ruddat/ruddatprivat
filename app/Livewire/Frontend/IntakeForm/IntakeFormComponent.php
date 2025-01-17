<?php

namespace App\Livewire\Frontend\IntakeForm;

use Livewire\Component;
use App\Models\ModIntakeForm;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Models\TelegraphBot;
use DefStudio\Telegraph\Models\TelegraphChat;

class IntakeFormComponent extends Component
{
    public $name, $email, $services = [], $timeline, $budget, $projectDetails, $referral, $additionalComments;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'services' => 'required|array|min:1',
        'timeline' => 'required|string|max:255',
        'budget' => 'required|string|max:255',
        'referral' => 'nullable|string|max:255',
        'projectDetails' => 'nullable|string',
        'additionalComments' => 'nullable|string',
    ];

    public function submit()
    {
        $this->validate();

        $ipAddress = request()->ip();

        // Prüfen, ob das Formular in den letzten 5 Minuten 3 Mal gesendet wurde
        $recentSubmissions = ModIntakeForm::where('ip_address', $ipAddress)
            ->where('created_at', '>=', Carbon::now()->subMinutes(5))
            ->count();

        if ($recentSubmissions >= 3) {
            $lastSubmission = ModIntakeForm::where('ip_address', $ipAddress)
                ->latest('created_at')
                ->first();

            if ($lastSubmission && $lastSubmission->created_at->diffInMinutes() < 30) {
                session()->flash('error', 'Sie haben das Formular zu oft gesendet. Bitte warten Sie 30 Minuten, bevor Sie es erneut versuchen.');
                return;
            }

            session()->flash('error', 'Sie haben das Formular zu oft gesendet. Es ist für 30 Minuten gesperrt.');
            return;
        }

        // Prüfen, ob die E-Mail von einer Wegwerf-Domain stammt
        if (!$this->isDisposableEmail($this->email)) {
            session()->flash('error', 'Ungültige E-Mail-Adresse. Bitte geben Sie eine echte E-Mail-Adresse ein.');
            return;
        }

        // Formular speichern
        ModIntakeForm::create([
            'name' => $this->name,
            'email' => $this->email,
            'services' => json_encode($this->services),
            'timeline' => $this->timeline,
            'budget' => $this->budget,
            'referral' => $this->referral,
            'project_details' => $this->projectDetails,
            'additional_comments' => $this->additionalComments,
            'ip_address' => $ipAddress,
        ]);

        // Telegram-Nachricht senden
        $this->sendTelegramMessage();

        session()->flash('success', 'Vielen Dank! Ihre Anfrage wurde erfolgreich gesendet.');
        $this->reset();
    }

    private function isDisposableEmail($email)
    {
        $domain = substr(strrchr($email, "@"), 1);
        return checkdnsrr($domain, "MX");
    }

    public function sendTelegramMessage()
    {


        //Telegraph::message('hello')->send();


        $bot = TelegraphChat::first(); // Hole den ersten konfigurierten Bot aus der Datenbank

//dd($bot);

        $message = "Neue Anfrage eingegangen:\n";
        $message .= "Name: {$this->name}\n";
        $message .= "E-Mail: {$this->email}\n";
        $message .= "Dienste: " . implode(', ', $this->services) . "\n";
        $message .= "Zeitrahmen: {$this->timeline}\n";
        $message .= "Budget: {$this->budget}\n";
        $message .= "Details: {$this->projectDetails}\n";
        $message .= "Empfehlung: {$this->referral}\n";
        $message .= "Zusätzliche Kommentare: {$this->additionalComments}";

        $bot->message($message)
          //  ->to(env('TELEGRAM_CHAT_ID')) // Die Chat-ID aus der .env-Datei
            ->send();
    }

    public function render()
    {
        return view('livewire.frontend.intake-form.intake-form-component');
    }
}
