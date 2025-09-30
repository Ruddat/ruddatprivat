{{-- resources/views/livewire/backend/customer/feedback-board.blade.php --}}
<div class="p-6">
    {{-- Button für Modal --}}
    <button wire:click="$set('showForm', true)"
        class="px-5 py-2 mb-6 bg-pink-600 text-white rounded-lg shadow hover:bg-pink-700 transition">
        + Feedback geben
    </button>

    {{-- Feedback Liste --}}
    <div class="space-y-4">
        @forelse ($feedbacks as $fb)
            <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow hover:shadow-md transition flex justify-between items-start">
                {{-- Left: Content --}}
                <div>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 text-lg">{{ $fb->title }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $fb->message }}</div>
                    <div class="mt-2 text-xs flex items-center gap-2">
                        @if ($fb->status === 'open')
                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded">Offen</span>
                        @elseif ($fb->status === 'in_progress')
                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded">In Arbeit</span>
                        @else
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded">Erledigt</span>
                        @endif
                        <span class="text-gray-400">•</span>
                        <span class="text-gray-500">Kategorie: {{ ucfirst($fb->category) }}</span>
                    </div>
                </div>

                {{-- Right: Voting --}}
                <div class="flex flex-col items-center space-y-1">
                    {{-- Upvote --}}
                    <button wire:click="vote({{ $fb->id }}, true)"
                        class="text-xl {{ $fb->user_vote === true 
                                    ? 'text-green-600 scale-110' 
                                    : 'text-gray-400 hover:text-green-500' }} transition">
                        👍
                    </button>

                    {{-- Votes --}}
                    <span class="font-semibold text-gray-800 dark:text-gray-200">
                        {{ $fb->votes >= 0 ? '+' . $fb->votes : $fb->votes }}
                    </span>

                    {{-- Downvote --}}
                    <button wire:click="vote({{ $fb->id }}, false)"
                        class="text-xl {{ $fb->user_vote === false 
                                    ? 'text-red-600 scale-110' 
                                    : 'text-gray-400 hover:text-red-500' }} transition">
                        👎
                    </button>
                </div>
            </div>
        @empty
            <div class="text-gray-500 text-center py-10 bg-gray-50 rounded-lg">
                Noch kein Feedback vorhanden.
            </div>
        @endforelse
    </div>

    {{-- Modal --}}
    <x-modal wire:model="showForm">
        <livewire:backend.customer.feedback-form />
    </x-modal>
</div>
