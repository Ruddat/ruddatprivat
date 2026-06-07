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
        if (!value) {
            return null;
        }

        var pattern = new RegExp(method + '\\((\\d+)');
        var match = value.match(pattern);

        return match ? parseInt(match[1], 10) : null;
    }

    function getLivewireComponent(element) {
        var root = element.closest('[wire\\:id]');

        if (!root || !window.Livewire) {
            return null;
        }

        return window.Livewire.find(root.getAttribute('wire:id'));
    }

    function refreshCardIds(cardsContainer) {
        Array.from(cardsContainer.children).forEach(function (cardElement) {
            var opener = cardElement.querySelector('[wire\\:click^="openCard("]');
            var cardId = opener ? parseId(opener.getAttribute('wire:click'), 'openCard') : null;

            if (cardId) {
                cardElement.dataset.projecthubCardId = String(cardId);
                cardElement.style.cursor = 'grab';
            }
        });
    }

    function initProjectHubSortable() {
        if (!window.Sortable || !window.Livewire) {
            return;
        }

        // Find all card containers by looking for the space-y-3 class inside list columns
        var columns = document.querySelectorAll('.w-80.bg-gray-50');

        columns.forEach(function (column) {
            var cardsContainer = column.querySelector('.space-y-3, .space-y-4');
            if (!cardsContainer) {
                return;
            }

            // Try to get listId from the createCard form
            var createForm = column.querySelector('form[wire\\:submit\\.prevent]');
            var listId = null;

            if (createForm) {
                var submitAttr = createForm.getAttribute('wire:submit.prevent') || '';
                listId = parseId(submitAttr, 'createCard');
            }

            // Fallback: try to get listId from data attribute
            if (!listId && cardsContainer.dataset.projecthubListId) {
                listId = parseInt(cardsContainer.dataset.projecthubListId, 10);
            }

            if (!listId) {
                return;
            }

            cardsContainer.dataset.projecthubListId = String(listId);
            refreshCardIds(cardsContainer);

            // Skip if already initialized
            if (initializedContainers.has(cardsContainer)) {
                return;
            }

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
                    if (event.item) {
                        event.item.style.cursor = 'grabbing';
                    }
                },
                onEnd: function (event) {
                    if (event.item) {
                        event.item.style.cursor = 'grab';
                    }

                    var target = event.to;
                    refreshCardIds(target);

                    var targetListId = parseInt(target.dataset.projecthubListId, 10);
                    var orderedCardIds = Array.from(target.children)
                        .map(function (item) {
                            return parseInt(item.dataset.projecthubCardId, 10);
                        })
                        .filter(Boolean);

                    var component = getLivewireComponent(target);

                    if (component && targetListId) {
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

    // Use a debounced approach for Livewire DOM updates to avoid re-initializing too often
    var updateTimer = null;
    document.addEventListener('livewire:update', function () {
        clearTimeout(updateTimer);
        updateTimer = setTimeout(initProjectHubSortable, 100);
    });
    document.addEventListener('livewire:updated', function () {
        clearTimeout(updateTimer);
        updateTimer = setTimeout(initProjectHubSortable, 100);
    });

    if (document.readyState !== 'loading') {
        boot();
    }
})();
