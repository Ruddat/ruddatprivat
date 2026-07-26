const SDK_BASE_URL = 'https://cdn.jsdelivr.net/gh/numberwolf/h265web.js@master/static/';
const SDK_SCRIPT_URL = `${SDK_BASE_URL}h265web.js`;

let sdkPromise = null;
const activePlayers = new WeakMap();

const isMovSource = (element) => {
    const source = element.querySelector('source');
    const src = source?.src || element.src || '';
    const mime = (source?.type || element.getAttribute('type') || '').toLowerCase();

    return mime === 'video/quicktime' || /\.mov(?:$|[?#])/i.test(src);
};

const browserSupportsMov = () => {
    const probe = document.createElement('video');
    return probe.canPlayType('video/quicktime') !== '';
};

const loadSdk = () => {
    if (typeof window.H265webjsPlayer === 'function') {
        return Promise.resolve(window.H265webjsPlayer);
    }

    if (sdkPromise) {
        return sdkPromise;
    }

    sdkPromise = new Promise((resolve, reject) => {
        const existing = document.querySelector(`script[src="${SDK_SCRIPT_URL}"]`);

        if (existing) {
            existing.addEventListener('load', () => resolve(window.H265webjsPlayer), { once: true });
            existing.addEventListener('error', () => reject(new Error('h265web.js konnte nicht geladen werden.')), { once: true });
            return;
        }

        const script = document.createElement('script');
        script.src = SDK_SCRIPT_URL;
        script.async = true;
        script.crossOrigin = 'anonymous';
        script.addEventListener('load', () => {
            if (typeof window.H265webjsPlayer !== 'function') {
                reject(new Error('h265web.js wurde geladen, stellt aber keinen Player bereit.'));
                return;
            }

            resolve(window.H265webjsPlayer);
        }, { once: true });
        script.addEventListener('error', () => reject(new Error('h265web.js konnte nicht geladen werden.')), { once: true });
        document.head.append(script);
    });

    return sdkPromise;
};

const showFallback = (container, message) => {
    container.innerHTML = '';

    const notice = document.createElement('div');
    notice.className = 'flex h-full min-h-48 flex-col items-center justify-center gap-3 bg-slate-950 p-6 text-center text-sm text-slate-300';
    notice.innerHTML = `
        <strong class="text-base text-white">MOV-Vorschau nicht verfügbar</strong>
        <span>${message}</span>
    `;

    container.append(notice);
};

const initializeMovPlayer = async (video) => {
    if (video.dataset.h265webReady === 'true' || browserSupportsMov()) {
        return;
    }

    video.dataset.h265webReady = 'true';

    const source = video.querySelector('source');
    const mediaUrl = source?.src || video.src;

    if (!mediaUrl) {
        return;
    }

    const container = document.createElement('div');
    container.id = `h265web-${crypto.randomUUID?.() || Math.random().toString(36).slice(2)}`;
    container.className = `${video.className || ''} min-h-48 w-full overflow-hidden bg-black`;
    container.dataset.h265webContainer = 'true';
    container.setAttribute('aria-label', 'MOV-Videoplayer');

    video.replaceWith(container);

    try {
        const createPlayer = await loadSdk();
        const player = createPlayer();

        player.on_ready_show_done_callback = () => {
            try {
                player.play();
            } catch (_) {
                // Autoplay may be blocked. The SDK controls remain usable.
            }
        };

        player.video_probe_callback = () => {};
        player.build({
            player_id: container.id,
            base_url: SDK_BASE_URL,
            wasm_js_uri: 'h265web_wasm.js',
            wasm_wasm_uri: 'h265web_wasm.wasm',
            ext_src_js_uri: 'extjs.js',
            ext_wasm_js_uri: 'extwasm.js',
            width: '100%',
            height: container.closest('.drive-viewer-content') ? 640 : 320,
            color: '#000000',
            auto_play: true,
            readframe_multi_times: -1,
            ignore_audio: false,
        });
        player.load_media(mediaUrl);

        activePlayers.set(container, player);
    } catch (error) {
        console.error(error);
        showFallback(container, 'Der Spezialplayer konnte diese Datei nicht öffnen. Das Original kann weiterhin heruntergeladen werden.');
    }
};

export const initializeH265WebPlayers = (root = document) => {
    const scope = root instanceof Element || root instanceof Document ? root : document;

    scope.querySelectorAll('video').forEach((video) => {
        if (isMovSource(video)) {
            initializeMovPlayer(video);
        }
    });
};

export const releaseH265WebPlayers = (root = document) => {
    const scope = root instanceof Element || root instanceof Document ? root : document;

    scope.querySelectorAll('[data-h265web-container]').forEach((container) => {
        const player = activePlayers.get(container);

        try {
            player?.release?.();
        } catch (_) {
            // SDK cleanup only.
        }

        activePlayers.delete(container);
    });
};

document.addEventListener('DOMContentLoaded', () => initializeH265WebPlayers());
document.addEventListener('livewire:navigated', () => initializeH265WebPlayers());
document.addEventListener('drive-viewer:opened', (event) => {
    initializeH265WebPlayers(event.detail?.root ?? document);
});
document.addEventListener('drive-viewer:cleanup', (event) => {
    releaseH265WebPlayers(event.detail?.root ?? document);
});
document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', ({ el }) => initializeH265WebPlayers(el));
});
