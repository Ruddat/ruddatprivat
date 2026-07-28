// Native HTML5 Media Player - Einfach und stabil ohne externe Abhängigkeiten

export const initializeMediaPlayers = (root = document) => {
    const scope = root instanceof Element || root instanceof Document ? root : document;

    scope.querySelectorAll('video:not([data-player-ready]), audio:not([data-player-ready]), [data-media-player]:not([data-player-ready])')
        .forEach((element) => {
            // Skip wenn bereits von MOV-Playern verarbeitet
            if (
                element.closest('.uppy-Dashboard') ||
                element.dataset.moviPlayerReady === 'true' ||
                element.dataset.h265webReady === 'true'
            ) {
                return;
            }

            element.dataset.playerReady = 'true';

            // Native HTML5 Controls aktivieren
            element.controls = true;
            element.preload = 'metadata';

            // Styling für bessere UX
            element.style.width = '100%';
            element.style.height = 'auto';
            element.style.backgroundColor = '#000';

            // Für Videos: Object-fit anpassen
            if (element.tagName.toLowerCase() === 'video') {
                element.style.objectFit = 'contain';
            }

            // Error Handling
            element.addEventListener('error', (event) => {
                console.warn('[Media Player] Fehler beim Laden:', event);
            });

            // Debug Info
            console.debug('[Media Player] Initialisiert:', {
                element: element.tagName,
                src: element.currentSrc || element.src,
                type: element.getAttribute('type')
            });
        });
};

export const releaseMediaPlayers = (root = document) => {
    const scope = root instanceof Element || root instanceof Document ? root : document;

    scope.querySelectorAll('[data-player-ready]')
        .forEach((element) => {
            // Cleanup: Player pausieren
            try {
                if (element.pause && typeof element.pause === 'function') {
                    element.pause();
                }
            } catch (error) {
                console.debug('[Media Player] Cleanup fehler:', error);
            }

            // Dataset cleanup
            delete element.dataset.playerReady;
        });
};

document.addEventListener('DOMContentLoaded', () => {
    initializeMediaPlayers();
});

document.addEventListener('livewire:navigated', () => {
    initializeMediaPlayers();
});

document.addEventListener('drive-viewer:opened', (event) => {
    initializeMediaPlayers(event.detail?.root ?? document);
});

document.addEventListener('drive-viewer:cleanup', (event) => {
    releaseMediaPlayers(event.detail?.root ?? document);
});

document.addEventListener('livewire:init', () => {
    if (typeof Livewire === 'undefined') {
        return;
    }

    Livewire.hook('morph.updated', ({ el }) => {
        initializeMediaPlayers(el);
    });
});

// Page Unload Cleanup
window.addEventListener('beforeunload', () => {
    releaseMediaPlayers();
});
