const viewerState = {
    items: [],
    index: -1,
    overlay: null,
    content: null,
    title: null,
    counter: null,
    download: null,
};

const stopMedia = (root) => {
    root?.querySelectorAll('video, audio').forEach((media) => {
        try {
            media.pause();
            media.removeAttribute('src');
            media.querySelectorAll('source').forEach((source) => source.removeAttribute('src'));
            media.load();
        } catch (_) {
            // Browser cleanup only.
        }
    });
};

const closeViewer = () => {
    if (!viewerState.overlay || viewerState.overlay.hidden) {
        return;
    }

    stopMedia(viewerState.content);
    viewerState.content.replaceChildren();
    viewerState.overlay.hidden = true;
    document.documentElement.style.overflow = '';
    viewerState.index = -1;
};

const createMediaElement = (item) => {
    if (item.type === 'image') {
        const image = document.createElement('img');
        image.src = item.src;
        image.alt = item.title;
        image.className = 'drive-viewer-image';
        return image;
    }

    if (item.type === 'video') {
        const video = document.createElement('video');
        video.controls = true;
        video.autoplay = true;
        video.preload = 'metadata';
        video.playsInline = true;
        video.dataset.mediaPlayer = 'true';
        video.className = 'drive-viewer-video';

        const source = document.createElement('source');
        source.src = item.src;
        source.type = item.mime;
        video.append(source);
        return video;
    }

    if (item.type === 'audio') {
        const wrapper = document.createElement('div');
        wrapper.className = 'drive-viewer-audio-shell';

        const icon = document.createElement('div');
        icon.className = 'drive-viewer-audio-icon';
        icon.textContent = '♪';

        const audio = document.createElement('audio');
        audio.controls = true;
        audio.autoplay = true;
        audio.preload = 'metadata';
        audio.dataset.mediaPlayer = 'true';

        const source = document.createElement('source');
        source.src = item.src;
        source.type = item.mime;
        audio.append(source);

        wrapper.append(icon, audio);
        return wrapper;
    }

    const frame = document.createElement('iframe');
    frame.src = item.src;
    frame.title = item.title;
    frame.className = 'drive-viewer-frame';
    frame.loading = 'eager';
    return frame;
};

const showItem = (index) => {
    if (!viewerState.items.length) {
        return;
    }

    const normalizedIndex = (index + viewerState.items.length) % viewerState.items.length;
    const item = viewerState.items[normalizedIndex];

    stopMedia(viewerState.content);
    viewerState.content.replaceChildren(createMediaElement(item));
    viewerState.title.textContent = item.title;
    viewerState.counter.textContent = `${normalizedIndex + 1} / ${viewerState.items.length}`;
    viewerState.download.href = item.download || item.src;
    viewerState.index = normalizedIndex;

    document.dispatchEvent(new CustomEvent('drive-viewer:opened', {
        detail: { root: viewerState.content },
    }));
};

const openViewer = (index) => {
    if (!viewerState.overlay) {
        return;
    }

    viewerState.overlay.hidden = false;
    document.documentElement.style.overflow = 'hidden';
    showItem(index);
    viewerState.overlay.querySelector('[data-drive-viewer-close]')?.focus();
};

const buildOverlay = () => {
    if (document.getElementById('drive-media-viewer')) {
        return document.getElementById('drive-media-viewer');
    }

    const overlay = document.createElement('div');
    overlay.id = 'drive-media-viewer';
    overlay.className = 'drive-viewer';
    overlay.hidden = true;
    overlay.innerHTML = `
        <div class="drive-viewer-backdrop" data-drive-viewer-close></div>
        <section class="drive-viewer-panel" role="dialog" aria-modal="true" aria-labelledby="drive-viewer-title">
            <header class="drive-viewer-header">
                <div class="drive-viewer-heading">
                    <strong id="drive-viewer-title"></strong>
                    <span data-drive-viewer-counter></span>
                </div>
                <div class="drive-viewer-actions">
                    <a data-drive-viewer-download class="drive-viewer-button" download>Download</a>
                    <button type="button" class="drive-viewer-button" data-drive-viewer-close aria-label="Viewer schließen">Schließen</button>
                </div>
            </header>
            <button type="button" class="drive-viewer-nav drive-viewer-prev" data-drive-viewer-prev aria-label="Vorherige Datei">‹</button>
            <div class="drive-viewer-content"></div>
            <button type="button" class="drive-viewer-nav drive-viewer-next" data-drive-viewer-next aria-label="Nächste Datei">›</button>
        </section>
    `;

    document.body.append(overlay);

    overlay.querySelectorAll('[data-drive-viewer-close]').forEach((button) => {
        button.addEventListener('click', closeViewer);
    });
    overlay.querySelector('[data-drive-viewer-prev]').addEventListener('click', () => showItem(viewerState.index - 1));
    overlay.querySelector('[data-drive-viewer-next]').addEventListener('click', () => showItem(viewerState.index + 1));

    viewerState.overlay = overlay;
    viewerState.content = overlay.querySelector('.drive-viewer-content');
    viewerState.title = overlay.querySelector('#drive-viewer-title');
    viewerState.counter = overlay.querySelector('[data-drive-viewer-counter]');
    viewerState.download = overlay.querySelector('[data-drive-viewer-download]');

    return overlay;
};

const detectItem = (article) => {
    const title = article.querySelector('h3')?.textContent?.trim() || 'Datei';
    const download = [...article.querySelectorAll('a')]
        .find((link) => link.textContent.trim() === 'Download')?.href;
    const video = article.querySelector('video source, video[src]');
    const audio = article.querySelector('audio source, audio[src]');
    const image = article.querySelector('img');
    const frame = article.querySelector('iframe');

    if (video) {
        return { article, type: 'video', src: video.src, mime: video.type || 'video/mp4', title, download };
    }

    if (audio) {
        return { article, type: 'audio', src: audio.src, mime: audio.type || 'audio/mpeg', title, download };
    }

    if (image) {
        return { article, type: 'image', src: image.src, mime: 'image/*', title, download };
    }

    if (frame) {
        return { article, type: 'pdf', src: frame.src, mime: 'application/pdf', title, download };
    }

    return null;
};

export const initializeDriveViewer = (root = document) => {
    buildOverlay();

    const scope = root instanceof Element || root instanceof Document ? root : document;
    const articles = [...scope.querySelectorAll('article')];
    const detected = articles.map(detectItem).filter(Boolean);

    if (!detected.length) {
        return;
    }

    viewerState.items = detected;

    detected.forEach((item, index) => {
        if (item.article.dataset.driveViewerReady === 'true') {
            return;
        }

        item.article.dataset.driveViewerReady = 'true';
        const preview = item.article.querySelector('.aspect-video');

        if (preview) {
            preview.classList.add('drive-viewer-trigger');
            preview.tabIndex = 0;
            preview.setAttribute('role', 'button');
            preview.setAttribute('aria-label', `${item.title} im Viewer öffnen`);

            const open = (event) => {
                if (event.target.closest('button, a, input, audio, video, iframe')) {
                    return;
                }
                openViewer(index);
            };

            preview.addEventListener('click', open);
            preview.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openViewer(index);
                }
            });
        }

        item.article.querySelectorAll('a').forEach((link) => {
            const label = link.textContent.trim();
            if (label === 'Vorschau' || label === 'Direkt öffnen') {
                link.removeAttribute('target');
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    openViewer(index);
                });
            }
        });
    });
};

document.addEventListener('keydown', (event) => {
    if (!viewerState.overlay || viewerState.overlay.hidden) {
        return;
    }

    if (event.key === 'Escape') {
        closeViewer();
    } else if (event.key === 'ArrowLeft') {
        showItem(viewerState.index - 1);
    } else if (event.key === 'ArrowRight') {
        showItem(viewerState.index + 1);
    }
});

document.addEventListener('DOMContentLoaded', () => initializeDriveViewer());
document.addEventListener('livewire:navigated', () => initializeDriveViewer());
document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', ({ el }) => initializeDriveViewer(el));
});
