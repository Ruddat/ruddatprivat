<?php

namespace App\Livewire\Frontend\SchedulingForm;

use Carbon\Carbon;
use Livewire\Component;

class SchedulingFormComponent extends Component
{
    public $selectedDate;

    public $selectedTime;

    public $name;

    public $email;

    public $phone;

    public $services = [];

    public $message;

    public $currentMonth;

    public $currentYear;

    public $daysInMonth = [];

    public $weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    public $currentStartDate;

    public $daysToShow = 7; // Anzahl der angezeigten Tage

    public $timeSlots = [];

    public $isSubmitted = false;

    protected $rules = [
        'selectedDate' => 'required|date',
        'selectedTime' => 'required|string',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'services' => 'required|array|min:1',
        'message' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        $today = Carbon::now();
        $this->currentStartDate = $today; // Setze den Starttag auf heute
        $this->selectedDate = $today->toDateString(); // Aktuelles Datum als ausgewählt
        $this->generateVisibleDays();
        $this->generateTimeSlots();
    }

    public function generateTimeSlots()
    {
        $this->timeSlots = [];
        $start = Carbon::createFromTime(10, 0); // Startzeit 08:00
        $end = Carbon::createFromTime(20, 0); // Endzeit 20:00
        $now = Carbon::now(); // Aktuelle Zeit

        $selectedDate = $this->selectedDate ? Carbon::parse($this->selectedDate) : $now;

        while ($start->lessThanOrEqualTo($end)) {
            // Blockiere Slots, wenn:
            // 1. Das ausgewählte Datum heute ist UND
            // 2. Der Slot in der Vergangenheit liegt oder innerhalb der nächsten 60 Minuten beginnt
            $isPast = $selectedDate->isToday() && $start->lessThanOrEqualTo($now->copy()->addHour());

            $this->timeSlots[] = [
                'time' => $start->format('H:i'), // 24-Stunden-Format
                'isPast' => $isPast,
            ];
            $start->addMinutes(30); // 30-Minuten-Intervalle
        }
    }

    public function changeDays($direction)
    {
        if ($direction === 'prev') {
            $this->currentStartDate = $this->currentStartDate->copy()->subDays($this->daysToShow);
        } elseif ($direction === 'next') {
            $this->currentStartDate = $this->currentStartDate->copy()->addDays($this->daysToShow);
        }
        $this->generateVisibleDays();
    }

    public function selectDate($date)
    {
        $this->selectedDate = Carbon::parse($date)->toDateString();
        $this->generateTimeSlots(); // Zeitslots basierend auf dem ausgewählten Datum aktualisieren
    }

    public function generateVisibleDays()
    {
        $this->daysInMonth = [];
        $now = Carbon::now();

        for ($i = 0; $i < $this->daysToShow; $i++) {
            $date = $this->currentStartDate->copy()->addDays($i);

            // Markiere Tage als vergangen, aber nicht den heutigen Tag
            $isPast = $date->isBefore($now->startOfDay());

            $this->daysInMonth[] = [
                'date' => $date,
                'isPast' => $isPast,
            ];
        }
    }

    public function updatedSelectedDate()
    {
        $this->generateTimeSlots();
    }

    public function generateDaysInMonth($month, $year)
    {
        $this->daysInMonth = [];
        $startOfMonth = Carbon::create($year, $month, 1);
        $daysInMonth = $startOfMonth->daysInMonth;
        $firstDayOfWeek = $startOfMonth->dayOfWeekIso; // 1 = Montag, 7 = Sonntag

        // Fülle die leeren Tage vor dem ersten Tag des Monats
        for ($i = 1; $i < $firstDayOfWeek; $i++) {
            $this->daysInMonth[] = null;
        }

        // Füge die Tage des Monats hinzu
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $this->daysInMonth[] = $day;
        }
    }

    public function changeMonth($direction)
    {
        if ($direction === 'prev') {
            $this->currentMonth--;
            if ($this->currentMonth < 1) {
                $this->currentMonth = 12;
                $this->currentYear--;
            }
        } elseif ($direction === 'next') {
            $this->currentMonth++;
            if ($this->currentMonth > 12) {
                $this->currentMonth = 1;
                $this->currentYear++;
            }
        }
        $this->generateDaysInMonth($this->currentMonth, $this->currentYear);
    }

    public function submit()
    {
        $this->validate();

        $meetingDetails = [
            'date' => $this->selectedDate,
            'time' => $this->selectedTime,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'services' => implode(', ', $this->services),
            'message' => $this->message,
        ];

        $this->sendToTelegram($meetingDetails);

        $this->isSubmitted = true; // Formular erfolgreich abgeschickt

        //  session()->flash('success', 'Ihr Termin wurde erfolgreich angefragt.');
        //  $this->reset();
    }

    private function sendToTelegram($details)
    {
        $bot = \DefStudio\Telegraph\Models\TelegraphBot::first();
        $chat = \DefStudio\Telegraph\Models\TelegraphChat::first();

        $message = "Neue Meeting-Anfrage:\n";
        $message .= "Datum: {$details['date']}\n";
        $message .= "Zeit: {$details['time']}\n";
        $message .= "Name: {$details['name']}\n";
        $message .= "E-Mail: {$details['email']}\n";
        $message .= "Telefon: {$details['phone']}\n";
        $message .= "Services: {$details['services']}\n";
        $message .= "Nachricht: {$details['message']}";

        if ($chat) {
            $chat->message($message)->send();
        }
    }

    public function resetForm()
    {
        $this->reset(); // Setzt alle Eigenschaften zurück
        $this->isSubmitted = false; // Zeigt das Formular erneut an
    }

    public function redirectToHome()
    {
        return redirect('/'); // Leitet zur Startseite weiter
    }

    public function render()
    {

        return view('livewire.frontend.scheduling-form.scheduling-form-component')
            ->layout('frontend.layouts.livewiere-app');
    }
}
