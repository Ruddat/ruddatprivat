const FALLBACK_EXTENSIONS = new Set(['mov', 'mkv', 'avi', 'm4v']);
const FALLBACK_MIME_TYPES = new Set([
    'video/quicktime',
    'video/x-matroska',
    'video/x-msvideo',
    'video/x-m4v',
]);

const extensionFromUrl = (url = '') => {
    try {
        const pathname = new URL(url, window.location.href).pathname;
        return pathname.split('.').pop()?.toLowerCase() || '';
    } catch (_) {
        return url.split(/[?#]/)[0].split('.').pop()?.toLowerCase() || '';
    }
};

export const getMediaSource = (element) => {
    const source = element.querySelector?.('source');
    const src = source?.src || element.src || '';
    const mime = (source?.type || element.getAttribute?.('type') || '').toLowerCase();

    return {
        src,
        mime,
        extension: extensionFromUrl(src),
    };
};

export const resolveMediaPlayer = (element) => {
    const tag = element.tagName?.toLowerCase();

    if (tag === 'audio') {
        return 'native';
    }

    if (tag !== 'video') {
        return 'native';
    }

    const { src, mime, extension } = getMediaSource(element);

    if (!src) {
        return 'native';
    }

    if (FALLBACK_MIME_TYPES.has(mime) || FALLBACK_EXTENSIONS.has(extension)) {
        const probe = document.createElement('video');
        const support = mime ? probe.canPlayType(mime) : '';

        return support === 'probably' ? 'native' : 'h265web';
    }

    return 'native';
};

export const isFallbackVideo = (element) => resolveMediaPlayer(element) === 'h265web';
