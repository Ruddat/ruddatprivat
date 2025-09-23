<div class="container mt-5 col-md-6" data-aos="ease-in" data-aos-offset="120" data-aos-duration="1000"
    data-aos-delay="100" style="margin-top: 150px !important; margin-bottom: 100px;">
    <div class="hidden"></div>
    <p class="text-center mb-4">Anfrageformular</p>
    <h2 class="mb-4 text-center">Helfen Sie uns, Sie besser zu unterstützen</h2>

    <form wire:submit.prevent="submit" class="card shadow-sm p-4">
        @if (session()->has("success"))
            <div class="alert alert-success" data-aos="fade-in" data-aos-duration="800">
                {{ session("success") }}
            </div>
        @endif

        <div class="mb-3">
            <label for="name" class="form-label">Name *</label>
            <input type="text" id="name" class="form-control" wire:model="name"
                placeholder="Max Mustermann">
            @error("name")
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-Mail-Adresse *</label>
            <input type="email" id="email" class="form-control" wire:model="email"
                placeholder="email@beispiel.de">
            @error("email")
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Für welche Dienstleistungen interessieren Sie sich?</label>
            <div>
                @foreach (["Webentwicklung", "Laravel-Entwicklung", "CSS-Design", "HTML5-Entwicklung", "Bootstrap-Entwicklung", "Java-Programmierung", "AJAX-Entwicklung", "MySQL-Datenbankverwaltung", "Electron-Anwendungen"] as $service)
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" wire:model="services"
                            value="{{ $service }}" id="service-{{ $loop->index }}">
                        <label class="form-check-label"
                            for="service-{{ $loop->index }}">{{ $service }}</label>
                    </div>
                @endforeach
            </div>
            @error("services")
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="timeline" class="form-label">Wie ist der Zeitrahmen für Ihr Projekt?</label>
            <select id="timeline" class="form-select" wire:model="timeline">
                <option value="" selected>Bitte auswählen</option>
                <option>Weniger als 1 Monat</option>
                <option>1-2 Wochen</option>
                <option>1 Monat</option>
                <option>3 Monate</option>
                <option>6 Monate oder länger</option>
            </select>
            @error("timeline")
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="budget" class="form-label">Welches Budget haben Sie vorgesehen?</label>
            <select id="budget" class="form-select" wire:model="budget">
                <option value="" selected>Bitte auswählen</option>
                <option>€1.000 - €5.000</option>
                <option>€5.000 - €10.000</option>
                <option>€10.000 oder mehr</option>
            </select>
            @error("budget")
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="projectDetails" class="form-label">Bitte beschreiben Sie Ihr Projekt im
                Detail</label>
            <textarea id="projectDetails" class="form-control" wire:model="projectDetails" rows="4"></textarea>
            @error("projectDetails")
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="referral" class="form-label">Wie sind Sie auf uns aufmerksam
                geworden?</label>
            <select id="referral" class="form-select" wire:model="referral">
                <option value="" selected>Bitte auswählen</option>
                <option>Empfehlung</option>
                <option>Soziale Medien</option>
                <option>Suchmaschine</option>
                <option>Sonstiges</option>
            </select>
            @error("referral")
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="additionalComments" class="form-label">Weitere Fragen oder
                Anmerkungen</label>
            <textarea id="additionalComments" class="form-control" wire:model="additionalComments"
                rows="4"></textarea>
            @error("additionalComments")
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        @if (session()->has("success"))
            <div class="alert alert-success">{{ session("success") }}</div>
        @elseif(session()->has("error"))
            <div class="alert alert-danger">{{ session("error") }}</div>
        @endif

        <button type="submit" class="btn btn-success btn-lg w-100">Absenden</button>
    </form>
</div>
<script>
    document.addEventListener('livewire:load', () => {
        // Reinitialize AOS after Livewire updates
        Livewire.hook('message.processed', (message, component) => {
            if (window.AOS) {
                AOS.refresh();
            }
        });
    });
</script>
