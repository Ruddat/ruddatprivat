            <div class="col-lg-7">
                @if (session()->has('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form wire:submit.prevent="submit" class="php-email-form">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <label for="name-field" class="pb-2">Ihr Name</label>
                            <input type="text" id="name-field" wire:model="name" class="form-control" required>
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email-field" class="pb-2">Ihre E-Mail</label>
                            <input type="email" id="email-field" wire:model="email" class="form-control" required>
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="subject-field" class="pb-2">Betreff</label>
                            <input type="text" id="subject-field" wire:model="subject" class="form-control" required>
                            @error('subject') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="message-field" class="pb-2">Nachricht</label>
                            <textarea id="message-field" wire:model="message" class="form-control" rows="6" required></textarea>
                            @error('message') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" id="data-consent" wire:model="dataConsent" class="form-check-input" required>
                                <label class="form-check-label" for="data-consent">
                                    Ich erlaube dieser Website, meine Angaben zu speichern, um auf meine Anfrage zu antworten. *
                                </label>
                                @error('dataConsent') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary">Nachricht senden</button>
                        </div>
                    </div>
                </form>
            </div>

