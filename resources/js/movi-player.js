import 'movi-player';

const activePlayers = new WeakSet();

const isMovSource = (video) => {
    const source = video.querySelector('source');

    const src = source?.src
        || source?.getAttribute('src')
        || video.src
        || video.getAttribute('src')
        || '';

    const mime = (
        source?.type
        || source?.getAttribute('type')
        || video.getAttribute('type')
        || ''
    ).toLowerCase();

    return (
        mime === 'video/quicktime'
        || /\.mov(?:$|[?#])/i.test(src)
    );
};

const getMediaUrl = (video) => {
    const source = video.querySelector('source');

    return source?.src
        || source?.getAttribute('src')
        || video.src
        || video.getAttribute('src')
        || '';
};

const replaceWithMoviPlayer = (video) => {
    if (!(video instanceof HTMLVideoElement)) {
        return;
    }

    if (
        video.dataset.moviPlayerReady === 'true'
        || activePlayers.has(video)
    ) {
        return;
    }

    const mediaUrl = getMediaUrl(video);

    if (!mediaUrl) {
        console.warn('[movi-player] Keine Medien-URL gefunden.');

        return;
    }

    video.dataset.moviPlayerReady = 'true';
    activePlayers.add(video);

    const player = document.createElement('movi-player');

    player.setAttribute('src', mediaUrl);
    player.setAttribute('controls', '');

    if (video.autoplay) {
        player.setAttribute('autoplay', '');
    }

    if (video.muted) {
        player.setAttribute('muted', '');
    }

    if (video.loop) {
        player.setAttribute('loop', '');
    }

    player.className = [
        video.className || '',
        'block',
        'w-full',
        'overflow-hidden',
        'bg-black',
    ].join(' ');

    player.style.width = '100%';
    player.style.height = video.closest('.drive-viewer-content')
        ? '640px'
        : '360px';

    player.dataset.moviPlayerContainer = 'true';
    player.setAttribute('aria-label', 'MOV-Videoplayer');

    video.replaceWith(player);

    console.debug('[movi-player] MOV-Player initialisiert.', {
        mediaUrl,
    });
};

export const initializeH265WebPlayers = (root = document) => {
    const scope = root instanceof Element || root instanceof Document
        ? root
        : document;

    if (
        scope instanceof HTMLVideoElement
        && isMovSource(scope)
    ) {
        replaceWithMoviPlayer(scope);
    }

    scope.querySelectorAll?.('video').forEach((video) => {
        if (isMovSource(video)) {
            replaceWithMoviPlayer(video);
        }
    });
};

export const releaseH265WebPlayers = (root = document) => {
    const scope = root instanceof Element || root instanceof Document
        ? root
        : document;

    scope
        .querySelectorAll?.('[data-movi-player-container]')
        .forEach((player) => {
            try {
                player.pause?.();
            } catch (_) {
                // Nur Cleanup.
            }

            player.remove();
        });
};

document.addEventListener('DOMContentLoaded', () => {
    initializeH265WebPlayers();
});

document.addEventListener('livewire:navigated', () => {
    initializeH265WebPlayers();
});

document.addEventListener('drive-viewer:opened', (event) => {
    initializeH265WebPlayers(
        event.detail?.root ?? document,
    );
});

document.addEventListener('drive-viewer:cleanup', (event) => {
    releaseH265WebPlayers(
        event.detail?.root ?? document,
    );
});

document.addEventListener('livewire:init', () => {
    if (typeof Livewire === 'undefined') {
        return;
    }

    Livewire.hook('morph.updated', ({ el }) => {
        initializeH265WebPlayers(el);
    });
});
