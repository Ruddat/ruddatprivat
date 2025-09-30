<div>
    <form wire:submit.prevent="save" class="space-y-4">
        <input type="file" wire:model="file" class="form-input">
        <button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded">Upload</button>
    </form>

    <div class="mt-6">
        <h3 class="font-bold mb-2">Hochgeladene Rechnungen</h3>
        <ul>
            @foreach($invoices as $inv)
                <li>
                    {{ $inv->invoice_date->format('d.m.Y') }} –
                    {{ number_format($inv->net_amount, 2, ',', '.') }} € Netto –
                    <a href="{{ Storage::url($inv->file_path) }}" target="_blank">PDF ansehen</a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
