<div class="container mt-5 col-md-6" data-aos="ease-in" data-aos-offset="120" data-aos-duration="1000" data-aos-delay="100" style="margin-top: 150px !important; margin-bottom: 100px;">
    @if ($isSubmitted)
<!-- Bestätigungsanzeige -->
<div class="card shadow-sm p-4 text-center">
    <h3>Vielen Dank für Ihre Anfrage!</h3>
    <p>Wir haben Ihre Terminanfrage erhalten und melden uns so bald wie möglich.</p>
    <p><strong>Datum:</strong> {{ $selectedDate }}</p>
    <p><strong>Uhrzeit:</strong> {{ $selectedTime }}</p>
    <p><strong>Services:</strong> {{ implode(', ', $services) }}</p>
    <p><strong>Name:</strong> {{ $name }}</p>
    <p><strong>E-Mail:</strong> {{ $email }}</p>
    <p><strong>Telefon:</strong> {{ $phone }}</p>
    <button wire:click="redirectToHome" class="btn btn-secondary mt-3">Zur Startseite</button>
</div>
    @else

    <h1 class="text-center">Termin-Anfrageformular</h1>
    <p class="text-center">Wir freuen uns darauf, Sie zu treffen</p>

    <!-- Kalender -->
        <div class="appointment-calendar bg-light p-4 rounded shadow-sm mb-4">
            <!-- Navigation -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button class="btn btn-outline-primary btn-sm" wire:click="changeDays('prev')">Vorherige</button>
                <h5 class="m-0">
                    {{ $currentStartDate ? $currentStartDate->format('F Y') : '' }}
                </h5>
                <button class="btn btn-outline-primary btn-sm" wire:click="changeDays('next')">Nächste</button>
            </div>

            <!-- Tage und Wochentage -->
            <div class="row text-center">
                @foreach ($daysInMonth as $day)
                    <div class="col">
                        <!-- Wochentag -->
                        <span class="d-block fw-bold mb-2">{{ $day['date']->format('D') }}</span>
                        <!-- Tag -->
                        <button
                            class="btn btn-sm day {{ $selectedDate === $day['date']->toDateString() ? 'active' : '' }} {{ $day['isPast'] ? 'disabled' : '' }}"
                            wire:click="selectDate('{{ $day['date']->toDateString() }}')"
                            @if ($day['isPast']) disabled @endif
                        >
                            {{ $day['date']->day }}
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Formular -->
        <form wire:submit.prevent="submit" class="card shadow-sm p-4">
            <!-- Datum -->
            <div class="mb-3">
                <label for="selectedDate" class="form-label">Ausgewähltes Datum *</label>
                <input type="text" id="selectedDate" wire:model="selectedDate" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label for="selectedTime" class="form-label">Wählen Sie eine Uhrzeit *</label>
                <select wire:model="selectedTime" id="selectedTime" class="form-select" required>
                    <option value="">Wählen Sie eine Uhrzeit</option>
                    @foreach ($timeSlots as $slot)
                        <option value="{{ $slot['time'] }}" @if ($slot['isPast']) disabled @endif>
                            {{ $slot['time'] }}
                        </option>
                    @endforeach
                </select>
                @error('selectedTime') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Name *</label>
            <input type="text" wire:model="name" id="name" class="form-control" placeholder="Ihr Name" required>
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <!-- E-Mail -->
        <div class="mb-3">
            <label for="email" class="form-label">E-Mail-Adresse *</label>
            <input type="email" wire:model="email" id="email" class="form-control" placeholder="email@beispiel.com" required>
            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <!-- Telefon -->
        <div class="mb-3">
            <label for="phone" class="form-label">Telefonnummer *</label>
            <input type="text" wire:model="phone" id="phone" class="form-control" placeholder="z.B. 0176-12345678" required>
            @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <!-- Services -->
        <div class="mb-3">
            <label for="services" class="form-label">Für welche Dienstleistung(en) interessieren Sie sich?</label>
            <div>
                <div class="form-check">
                    <input type="checkbox" wire:model="services" value="Professionelle Webentwicklung" id="service1" class="form-check-input">
                    <label for="service1" class="form-check-label">Professionelle Webentwicklung</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" wire:model="services" value="Individuelle Softwarelösungen" id="service2" class="form-check-input">
                    <label for="service2" class="form-check-label">Individuelle Softwarelösungen</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" wire:model="services" value="Interaktive Benutzeroberflächen" id="service3" class="form-check-input">
                    <label for="service3" class="form-check-label">Interaktive Benutzeroberflächen</label>
                </div>
            </div>
            @error('services') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <!-- Nachricht -->
        <div class="mb-3">
            <label for="message" class="form-label">Nachricht</label>
            <textarea wire:model="message" id="message" class="form-control" rows="4" placeholder="Ihre Nachricht"></textarea>
            @error('message') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <!-- Absenden -->
        <button type="submit" class="btn btn-success btn-lg w-100">Anfrage senden</button>
    </form>
    @endif

<style>
    .day {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #f8f9fa;
        color: #000;
        border: 2px solid #ddd;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: all 0.3s ease;
    }

    .day:hover:not(.disabled) {
        background-color: #007bff;
        color: white;
        transform: scale(1.1);
    }

    .day.active {
        background-color: #007bff;
        color: white;
        border-color: #0056b3;
    }

    .day.disabled {
        color: #888;
        text-decoration: line-through;
        cursor: not-allowed;
        background-color: #e9ecef;
    }

    .appointment-calendar .col {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .appointment-calendar .col span {
        font-size: 12px;
        color: #555;
    }

    select option:disabled {
        color: #888;
        background-color: #f8f9fa;
    }

</style>
</div>
