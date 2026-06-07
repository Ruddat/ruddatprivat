(function () {
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

        var createForms = Array.from(document.querySelectorAll('form[wire\\:submit\\.prevent]'))
            .filter(function (form) {
                return (form.getAttribute('wire:submit.prevent') || '').indexOf('createCard(') === 0;
            });

        createForms.forEach(function (form) {
            var listId = parseId(form.getAttribute('wire:submit.prevent'), 'createCard');
            var column = form.closest('.w-80');

            if (!listId || !column) {
                return;
            }

            var cardsContainer = column.querySelector('.space-y-3, .space-y-4');

            if (!cardsContainer) {
                return;
            }

            cardsContainer.dataset.projecthubListId = String(listId);
            refreshCardIds(cardsContainer);

            if (cardsContainer.dataset.projecthubSortableReady === '1') {
                return;
            }

            cardsContainer.dataset.projecthubSortableReady = '1';

            new window.Sortable(cardsContainer, {
                group: 'projecthub-cards',
                animation: 150,
                ghostClass: 'opacity-50',
                dragClass: 'ring-2',
                filter: 'input, textarea, select, a',
                preventOnFilter: false,
                delay: 120,
                delayOnTouchOnly: false,
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
    document.addEventListener('livewire:update', boot);
    document.addEventListener('livewire:updated', boot);

    if (document.readyState !== 'loading') {
        boot();
    }
})();
