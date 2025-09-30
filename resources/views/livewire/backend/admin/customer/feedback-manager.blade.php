{{-- resources\views\livewire\backend\admin\customer\feedback-manager.blade.php --}}
<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">Kunden-Feedback</h2>

    {{-- Filter --}}
    <div class="flex items-center gap-4 mb-4">
        <label class="text-sm text-gray-600">Status:</label>
        <select wire:model="statusFilter" class="form-select w-40">
            <option value="all">Alle</option>
            <option value="open">Offen</option>
            <option value="in_progress">In Bearbeitung</option>
            <option value="done">Erledigt</option>
        </select>
    </div>

    {{-- Feedback Tabelle --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Titel</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Kategorie</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
                    <th class="px-4 py-2 text-center font-medium text-gray-600">Votes</th>
                    <th class="px-4 py-2 text-right font-medium text-gray-600">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($feedbacks as $fb)
                    <tr>
                        <td class="px-4 py-2">
                            <div class="font-semibold text-gray-900">{{ $fb->title }}</div>
                            <div class="text-xs text-gray-500">{{ Str::limit($fb->message, 80) }}</div>
                        </td>
                        <td class="px-4 py-2">
                            @if ($fb->category === 'bug')
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">🐞 Bug</span>
                            @elseif ($fb->category === 'feature')
                                <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">⚙️ Feature</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">💡 Idee</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if ($fb->status === 'open')
                                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">Offen</span>
                            @elseif ($fb->status === 'in_progress')
                                <span class="px-2 py-1 text-xs rounded bg-indigo-100 text-indigo-700">In Bearbeitung</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Erledigt</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-center font-semibold text-gray-800">
                            {{ $fb->votes }}
                        </td>
                        <td class="px-4 py-2 text-right space-x-2">
                            {{-- Status Buttons --}}
                            <button wire:click="setStatus({{ $fb->id }}, 'open')"
                                class="px-2 py-1 text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded">Offen</button>
                            <button wire:click="setStatus({{ $fb->id }}, 'in_progress')"
                                class="px-2 py-1 text-xs bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded">In Arbeit</button>
                            <button wire:click="setStatus({{ $fb->id }}, 'done')"
                                class="px-2 py-1 text-xs bg-green-100 hover:bg-green-200 text-green-700 rounded">Fertig</button>

                            {{-- Voting --}}
                            <button wire:click="vote({{ $fb->id }}, true)"
                                class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded">👍</button>
                            <button wire:click="vote({{ $fb->id }}, false)"
                                class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded">👎</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                            Kein Feedback vorhanden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $feedbacks->links() }}
    </div>
</div>
