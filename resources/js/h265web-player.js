const SDK_BASE_URL = 'https://cdn.jsdelivr.net/gh/numberwolf/h265web.js@master/static/';
const SDK_SCRIPT_URL = `${SDK_BASE_URL}h265web.js`;

let sdkPromise = null;
let playerIsStarting = false;

const activePlayers = new WeakMap();

const isMovSource = (element) => {
    const source = element.querySelector('source');

    const src = source?.src
        || source?.getAttribute('src')
        || element.src
        || element.getAttribute('src')
        || '';

    const mime = (
        source?.type
        || source?.getAttribute('type')
        || element.getAttribute('type')
        || ''
    ).toLowerCase();

    return (
        mime === 'video/quicktime'
        || /\.mov(?:$|[?#])/i.test(src)
    );
};

const browserProbablySupportsMov = () => {
    const probe = document.createElement('video');

    return probe.canPlayType('video/quicktime') === 'probably';
};

const loadSdk = () => {
    if (typeof window.H265webjsPlayer === 'function') {
        return Promise.resolve(window.H265webjsPlayer);
    }

    if (sdkPromise) {
        return sdkPromise;
    }

    sdkPromise = new Promise((resolve, reject) => {
        const existing = document.querySelector(
            `script[src="${SDK_SCRIPT_URL}"]`,
        );

        if (existing) {
            existing.addEventListener(
                'load',
                () => {
                    if (typeof window.H265webjsPlayer !== 'function') {
                        reject(
                            new Error(
                                'h265web.js wurde geladen, stellt aber keinen Player bereit.',
                            ),
                        );

                        return;
                    }

                    resolve(window.H265webjsPlayer);
                },
                { once: true },
            );

            existing.addEventListener(
                'error',
                () => {
                    reject(
                        new Error('h265web.js konnte nicht geladen werden.'),
                    );
                },
                { once: true },
            );

            return;
        }

        const script = document.createElement('script');

        script.src = SDK_SCRIPT_URL;
        script.async = true;
        script.crossOrigin = 'anonymous';

        script.addEventListener(
            'load',
            () => {
                if (typeof window.H265webjsPlayer !== 'function') {
                    reject(
                        new Error(
                            'h265web.js wurde geladen, stellt aber keinen Player bereit.',
                        ),
                    );

                    return;
                }

                resolve(window.H265webjsPlayer);
            },
            { once: true },
        );

        script.addEventListener(
            'error',
            () => {
                reject(
                    new Error('h265web.js konnte nicht geladen werden.'),
                );
            },
            { once: true },
        );

        document.head.append(script);
    });

    return sdkPromise;
};

const showFallback = (container, message) => {
    container.innerHTML = '';

    const notice = document.createElement('div');

    notice.className = [
        'flex',
        'h-full',
        'min-h-48',
        'flex-col',
        'items-center',
        'justify-center',
        'gap-3',
        'bg-slate-950',
        'p-6',
        'text-center',
        'text-sm',
        'text-slate-300',
    ].join(' ');

    const headline = document.createElement('strong');
    headline.className = 'text-base text-white';
    headline.textContent = 'MOV-Vorschau nicht verfügbar';

    const description = document.createElement('span');
    description.textContent = message;

    notice.append(headline, description);
    container.append(notice);
};

const releaseContainer = (container) => {
    if (!(container instanceof Element)) {
        return;
    }

    const player = activePlayers.get(container);

    try {
        player?.release?.();
    } catch (error) {
        console.warn(
            '[h265web] Player konnte nicht sauber beendet werden.',
            error,
        );
    }

    activePlayers.delete(container);

    container.querySelector('#canvas')?.remove();
};

const releaseAllPlayers = () => {
    document
        .querySelectorAll('[data-h265web-container]')
        .forEach((container) => {
            releaseContainer(container);
        });
};

const createPlayerContainer = (video) => {
    /*
     * Das WASM-Modul von h265web.js erwartet ein Canvas mit der festen
     * ID "#canvas". Deshalb darf immer nur ein Spezialplayer gleichzeitig
     * aktiv sein.
     */
    releaseAllPlayers();

    document.getElementById('canvas')?.remove();

    const container = document.createElement('div');

    container.id = `h265web-${
        crypto.randomUUID?.()
        || Math.random().toString(36).slice(2)
    }`;

    container.className = [
        video.className || '',
        'relative',
        'min-h-48',
        'w-full',
        'overflow-hidden',
        'bg-black',
    ].join(' ');

    container.dataset.h265webContainer = 'true';
    container.setAttribute('aria-label', 'MOV-Videoplayer');

    const canvas = document.createElement('canvas');

    canvas.id = 'canvas';
    canvas.className = 'block h-full w-full bg-black';

    container.append(canvas);

    video.replaceWith(container);

    return container;
};

const initializeMovPlayer = async (video) => {
    if (!(video instanceof HTMLVideoElement)) {
        return;
    }

    if (
        playerIsStarting
        || video.dataset.h265webReady === 'true'
        || video.dataset.moviPlayerReady === 'true'
        || browserProbablySupportsMov()
    ) {
        return;
    }

    /*
     * Wenn bereits ein Spezialplayer im DOM vorhanden ist,
     * keinen zweiten Player parallel starten.
     */
    if (document.querySelector('[data-h265web-container]')) {
        return;
    }

    video.dataset.h265webReady = 'true';

    const source = video.querySelector('source');

    const mediaUrl = source?.src
        || source?.getAttribute('src')
        || video.src
        || video.getAttribute('src');

    if (!mediaUrl) {
        console.warn('[h265web] Keine Video-URL gefunden.');

        return;
    }

    playerIsStarting = true;

    const container = createPlayerContainer(video);

    try {
        const createPlayer = await loadSdk();

        if (!container.isConnected) {
            return;
        }

        const player = createPlayer();

        player.on_ready_show_done_callback = () => {
            console.debug('[h265web] Player bereit.', {
                mediaUrl,
                containerId: container.id,
            });
        };

        player.video_probe_callback = (probeData) => {
            console.debug('[h265web] Video untersucht.', probeData);
        };

        const playerWidth = Math.max(
            container.clientWidth,
            container.parentElement?.clientWidth || 0,
            640,
        );

        const playerHeight = container.closest('.drive-viewer-content')
            ? 640
            : 360;

        player.build({
            player_id: container.id,

            base_url: SDK_BASE_URL,
            wasm_js_uri: 'h265web_wasm.js',
            wasm_wasm_uri: 'h265web_wasm.wasm',
            ext_src_js_uri: 'extjs.js',
            ext_wasm_js_uri: 'extwasm.js',

            width: playerWidth,
            height: playerHeight,

            color: '#000000',
            auto_play: true,
            readframe_multi_times: -1,
            ignore_audio: false,
        });

        activePlayers.set(container, player);

        player.load_media(mediaUrl);
    } catch (error) {
        console.error('[h265web] Initialisierung fehlgeschlagen.', error);

        releaseContainer(container);

        showFallback(
            container,
            'Der Spezialplayer konnte diese Datei nicht öffnen. '
            + 'Das Original kann weiterhin heruntergeladen werden.',
        );
    } finally {
        playerIsStarting = false;
    }
};

export const initializeH265WebPlayers = (root = document) => {
    const scope = root instanceof Element || root instanceof Document
        ? root
        : document;

    const videos = [];

    if (
        scope instanceof HTMLVideoElement
        && isMovSource(scope)
    ) {
        videos.push(scope);
    }

    scope.querySelectorAll?.('video').forEach((video) => {
        if (isMovSource(video)) {
            videos.push(video);
        }
    });

    /*
     * h265web.js verwendet aktuell ein Canvas mit der festen ID "#canvas".
     * Daher nur das erste passende MOV-Video initialisieren.
     */
    const firstVideo = videos.find((video) => (
        video.isConnected
        && video.dataset.h265webReady !== 'true'
    ));

    if (firstVideo) {
        initializeMovPlayer(firstVideo);
    }
};

export const releaseH265WebPlayers = (root = document) => {
    const scope = root instanceof Element || root instanceof Document
        ? root
        : document;

    if (
        scope instanceof Element
        && scope.matches('[data-h265web-container]')
    ) {
        releaseContainer(scope);
    }

    scope
        .querySelectorAll?.('[data-h265web-container]')
        .forEach((container) => {
            releaseContainer(container);
        });
};

const cleanupObserver = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
        mutation.removedNodes.forEach((node) => {
            if (!(node instanceof Element)) {
                return;
            }

            if (node.matches('[data-h265web-container]')) {
                releaseContainer(node);
            }

            node
                .querySelectorAll?.('[data-h265web-container]')
                .forEach((container) => {
                    releaseContainer(container);
                });
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    if (document.body) {
        cleanupObserver.observe(document.body, {
            childList: true,
            subtree: true,
        });
    }

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

window.addEventListener('beforeunload', () => {
    releaseAllPlayers();
});
