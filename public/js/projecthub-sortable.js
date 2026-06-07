(function () {
    var initializedContainers = new WeakSet();

    // Inject SortableJS drag styles (single-class names only – classList.add cannot handle spaces)
    if (!document.getElementById('projecthub-sortable-styles')) {
        var style = document.createElement('style');
        style.id = 'projecthub-sortable-styles';
        style.textContent = [
            '.sortable-ghost { opacity: 0.4; }',
            '.sortable-chosen { box-shadow: 0 0 0 2px #818cf8; border-radius: 0.5rem; }',
            '.sortable-drag { box-shadow: 0 10px 25px -5px rgba(0,0,0,.25); transform: rotate(3deg); opacity: 0.95; }'
        ].join('\n');
        document.head.appendChild(style);
    }

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

    function initProjectHubSortable() {
        if (!window.Sortable || !window.Livewire) return;

        // Find all containers that should be sortable:
        // 1. Admin: .w-72.bg-gray-50 columns with .overflow-y-auto inside
        // 2. Public share: elements with .projecthub-sortable class
        // 3. Any element with data-projecthub-list-id that isn't initialized yet

        var selectors = [
            '.projecthub-sortable',
            '.w-72.bg-gray-50 .overflow-y-auto[data-projecthub-list-id]',
            '.w-80.bg-gray-50 .overflow-y-auto[data-projecthub-list-id]'
        ];

        // Also find containers by data attribute directly (share view has them on the card container)
        var dataContainers = document.querySelectorAll('[data-projecthub-list-id]');
        var allContainers = new Set();

        dataContainers.forEach(function (el) {
            allContainers.add(el);
        });

        selectors.forEach(function (sel) {
            document.querySelectorAll(sel).forEach(function (el) {
                allContainers.add(el);
            });
        });

        allContainers.forEach(function (cardsContainer) {
            var listId = parseInt(cardsContainer.dataset.projecthubListId, 10);
            if (!listId) return;

            // Ensure card elements have data-projecthub-card-id
            Array.from(cardsContainer.children).forEach(function (cardEl) {
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
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
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
