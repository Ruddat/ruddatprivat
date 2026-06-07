(function () {
    var initializedContainers = new WeakSet();

    function loadSortable(callback) {
        if (window.Sortable) {
            callback();
            return;
        }

        var existing = document.querySelector('script[data-projecthub-sortable-loader]');
        if (existing) {
            existing.addEventListener('load', callback, { once: true });
            return;
        }

        var script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js';
        script.async = true;
        script.dataset.projecthubSortableLoader = '1';
        script.onload = callback;
        document.head.appendChild(script);
    }

    function parseId(value, method) {
        if (!value) return null;
        var pattern = new RegExp(method + '\\((\\d+)');
        var match = value.match(pattern);
        return match ? parseInt(match[1], 10) : null;
    }

    function getLivewireComponent(element) {
        var root = element.closest('[wire\\:id]');
        if (!root || !window.Livewire) return null;
        return window.Livewire.find(root.getAttribute('wire:id'));
    }

    function getListIdFromColumn(column) {
        // Try data attribute first
        var cardsContainer = column.querySelector('[data-projecthub-list-id]');
        if (cardsContainer) {
            return parseInt(cardsContainer.dataset.projecthubListId, 10);
        }

        // Try from the createCard form
        var form = column.querySelector('form[wire\\:submit\\.prevent]');
        if (form) {
            var attr = form.getAttribute('wire:submit.prevent') || '';
            var id = parseId(attr, 'createCard');
            if (id) return id;
        }

        return null;
    }

    function initProjectHubSortable() {
        if (!window.Sortable || !window.Livewire) return;

        // Find columns: elements with the list structure
        var columns = document.querySelectorAll('.w-72.bg-gray-50, .w-80.bg-gray-50');

        columns.forEach(function (column) {
            // Find the scrollable cards container
            var cardsContainer = column.querySelector('.overflow-y-auto');
            if (!cardsContainer) return;

            var listId = getListIdFromColumn(column);
            if (!listId) return;

            cardsContainer.dataset.projecthubListId = String(listId);

            // Make sure card elements have the card ID
            Array.from(cardsContainer.children).forEach(function (cardEl) {
                // Already has data attribute from Blade
                if (!cardEl.dataset.projecthubCardId) {
                    var btn = cardEl.querySelector('[wire\\:click^="openCard("]');
                    if (btn) {
                        var cardId = parseId(btn.getAttribute('wire:click'), 'openCard');
                        if (cardId) {
                            cardEl.dataset.projecthubCardId = String(cardId);
                        }
                    }
                }
                if (cardEl.dataset.projecthubCardId) {
                    cardEl.style.cursor = 'grab';
                }
            });

            if (initializedContainers.has(cardsContainer)) return;
            initializedContainers.add(cardsContainer);

            new window.Sortable(cardsContainer, {
                group: 'projecthub-cards',
                animation: 150,
                ghostClass: 'opacity-50',
                dragClass: 'ring-2 ring-indigo-400',
                filter: 'input, textarea, select, a, button',
                preventOnFilter: false,
                delay: 120,
                delayOnTouchOnly: true,
                fallbackTolerance: 5,
                onStart: function (event) {
                    if (event.item) event.item.style.cursor = 'grabbing';
                },
                onEnd: function (event) {
                    if (event.item) event.item.style.cursor = 'grab';

                    var target = event.to;
                    var targetListId = parseInt(target.dataset.projecthubListId, 10);

                    var orderedCardIds = Array.from(target.children)
                        .map(function (item) {
                            return parseInt(item.dataset.projecthubCardId, 10);
                        })
                        .filter(Boolean);

                    var component = getLivewireComponent(target);
                    if (component && targetListId && orderedCardIds.length > 0) {
                        component.call('reorderCards', targetListId, orderedCardIds);
                    }
                }
            });
        });
    }

    function boot() {
        loadSortable(initProjectHubSortable);
    }

    document.addEventListener('DOMContentLoaded', boot);
    document.addEventListener('livewire:navigated', boot);

    var updateTimer = null;
    function debouncedInit() {
        clearTimeout(updateTimer);
        updateTimer = setTimeout(initProjectHubSortable, 150);
    }
    document.addEventListener('livewire:update', debouncedInit);
    document.addEventListener('livewire:updated', debouncedInit);

    if (document.readyState !== 'loading') boot();
})();
