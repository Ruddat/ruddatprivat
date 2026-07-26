import Plyr from 'plyr';
import 'plyr/dist/plyr.css';

export const initializeMediaPlayers = (root = document) => {
    const scope = root instanceof Element || root instanceof Document ? root : document;

    scope.querySelectorAll('video:not([data-player-ready]), audio:not([data-player-ready]), [data-media-player]:not([data-player-ready])')
        .forEach((element) => {
            if (element.closest('.uppy-Dashboard')) {
                return;
            }

            element.dataset.playerReady = 'true';

            const isVideo = element.tagName.toLowerCase() === 'video';
            const controls = [
                ...(isVideo ? ['play-large'] : []),
                'restart',
                'rewind',
                'play',
                'fast-forward',
                'progress',
                'current-time',
                'duration',
                'mute',
                'volume',
                'settings',
                ...(isVideo ? ['pip', 'airplay', 'fullscreen'] : []),
            ];

            element._plyr = new Plyr(element, {
                controls,
                settings: ['speed'],
                speed: {
                    selected: 1,
                    options: [0.5, 0.75, 1, 1.25, 1.5, 2],
                },
                seekTime: 10,
                keyboard: {
                    focused: true,
                    global: false,
                },
            });
        });
};

document.addEventListener('DOMContentLoaded', () => initializeMediaPlayers());
document.addEventListener('livewire:navigated', () => initializeMediaPlayers());
document.addEventListener('drive-viewer:opened', (event) => {
    initializeMediaPlayers(event.detail?.root ?? document);
});
document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', ({ el }) => initializeMediaPlayers(el));
});
