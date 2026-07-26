import Plyr from 'plyr';
import 'plyr/dist/plyr.css';

const initializeMediaPlayers = (root = document) => {
    root.querySelectorAll('[data-media-player]:not([data-player-ready])').forEach((element) => {
        element.dataset.playerReady = 'true';

        const player = new Plyr(element, {
            controls: [
                'play-large',
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
                'pip',
                'airplay',
                'fullscreen',
            ],
            settings: ['speed'],
            speed: {
                selected: 1,
                options: [0.5, 0.75, 1, 1.25, 1.5, 2],
            },
        });

        element._plyr = player;
    });
};

document.addEventListener('DOMContentLoaded', () => initializeMediaPlayers());
document.addEventListener('livewire:navigated', () => initializeMediaPlayers());
document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', ({ el }) => initializeMediaPlayers(el));
});
