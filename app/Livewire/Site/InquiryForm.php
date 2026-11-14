<?php

namespace App\Livewire\Site;

use App\Enums\InquiryType;
use App\Services\Inquiries\InquirySubmission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class InquiryForm extends Component
{
    /** Mindestzeit zwischen Anzeige und Absenden (Bot-Schutz). */
    public const MIN_SECONDS = 4;

    public const MAX_PER_IP_PER_HOUR = 5;

    public const MAX_PER_EMAIL_PER_HOUR = 3;

    public string $name = '';

    public string $company = '';

    public string $email = '';

    public string $phone = '';

    public string $type = '';

    public string $application_summary = '';

    public string $requirements = '';

    public bool $privacy = false;

    /** Honeypot – bleibt für Menschen unsichtbar und leer. */
    public string $website = '';

    #[Locked]
    public int $renderedAt = 0;

    public bool $submitted = false;

    public ?string $submittedReference = null;

    public function mount(?string $preset = null): void
    {
        $this->renderedAt = now()->getTimestamp();

        if ($preset && InquiryType::tryFrom($preset)) {
            $this->type = $preset;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'company' => ['required', 'string', 'min:2', 'max:160'],
            'email' => ['required', 'string', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+()\/\-\s]{5,40}$/'],
            'type' => ['required', Rule::enum(InquiryType::class)],
            'application_summary' => ['required', 'string', 'min:20', 'max:2000'],
            'requirements' => ['nullable', 'string', 'max:5000'],
            'privacy' => ['accepted'],
        ];
    }

    protected function messages(): array
    {
        // Deutsche Meldungen direkt hier, da die Anwendung global mit Locale "en" läuft
        return [
            'required' => 'Bitte füllen Sie das Feld „:attribute“ aus.',
            'email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'min' => '„:attribute“ muss mindestens :min Zeichen lang sein.',
            'max' => '„:attribute“ darf höchstens :max Zeichen lang sein.',
            'type.enum' => 'Bitte wählen Sie eine Art der Anfrage aus.',
            'phone.regex' => 'Bitte geben Sie eine gültige Telefonnummer ein.',
            'application_summary.min' => 'Bitte beschreiben Sie Ihre Anwendung in mindestens :min Zeichen.',
            'privacy.accepted' => 'Bitte bestätigen Sie die Datenschutzhinweise.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name' => 'Name',
            'company' => 'Unternehmen',
            'email' => 'E-Mail-Adresse',
            'phone' => 'Telefonnummer',
            'type' => 'Art der Anfrage',
            'application_summary' => 'Beschreibung der Anwendung',
            'requirements' => 'Technische Anforderungen',
        ];
    }

    public function submit(InquirySubmission $submission): void
    {
        // Bots: Honeypot ausgefüllt oder unrealistisch schnell → stillschweigend „erfolgreich“
        if ($this->website !== '' || (now()->getTimestamp() - $this->renderedAt) < self::MIN_SECONDS) {
            Log::notice('Anfrageformular: mutmaßlicher Spam verworfen.', ['honeypot' => $this->website !== '']);
            $this->finish(null);

            return;
        }

        $data = $this->validate();

        $ipKey = 'inquiry:ip:' . sha1((string) request()->ip());
        $mailKey = 'inquiry:mail:' . sha1(mb_strtolower($data['email']));

        if (RateLimiter::tooManyAttempts($ipKey, self::MAX_PER_IP_PER_HOUR)
            || RateLimiter::tooManyAttempts($mailKey, self::MAX_PER_EMAIL_PER_HOUR)) {
            $this->addError('form', 'Es wurden zu viele Anfragen in kurzer Zeit gesendet. Bitte versuchen Sie es später erneut oder schreiben Sie eine E-Mail.');

            return;
        }

        RateLimiter::hit($ipKey, 3600);
        RateLimiter::hit($mailKey, 3600);

        $result = $submission->submit([
            'name' => trim($data['name']),
            'company' => trim($data['company']),
            'email' => trim($data['email']),
            'phone' => trim($data['phone'] ?? ''),
            'type' => $data['type'],
            'application_summary' => trim($data['application_summary']),
            'requirements' => trim($data['requirements'] ?? ''),
        ], request()->ip());

        $this->finish($result['inquiry']->reference);
    }

    private function finish(?string $reference): void
    {
        $this->reset(['name', 'company', 'email', 'phone', 'type', 'application_summary', 'requirements', 'privacy', 'website']);
        $this->submitted = true;
        $this->submittedReference = $reference;
    }

    public function render()
    {
        return view('site.livewire.inquiry-form', [
            'types' => InquiryType::options(),
        ]);
    }
}
