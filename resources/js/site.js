// Schlankes Skript für die öffentliche RuddatTech-Webseite (ohne Abhängigkeiten).

function initMobileNav() {
    const toggle = document.querySelector('[data-nav-toggle]');
    const panel = document.getElementById('mobile-nav');

    if (!toggle || !panel) {
        return;
    }

    const focusables = () => panel.querySelectorAll('a, button');

    const open = () => {
        panel.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
        document.documentElement.classList.add('overflow-hidden');
        requestAnimationFrame(() => panel.classList.add('is-open'));
        focusables()[0]?.focus();
    };

    const close = (returnFocus = true) => {
        toggle.setAttribute('aria-expanded', 'false');
        document.documentElement.classList.remove('overflow-hidden');
        panel.classList.remove('is-open');
        panel.hidden = true;

        if (returnFocus) {
            toggle.focus();
        }
    };

    toggle.addEventListener('click', () => {
        toggle.getAttribute('aria-expanded') === 'true' ? close() : open();
    });

    panel.querySelectorAll('[data-nav-close]').forEach((el) => el.addEventListener('click', () => close()));
    panel.querySelectorAll('a').forEach((el) => el.addEventListener('click', () => close(false)));

    document.addEventListener('keydown', (event) => {
        if (panel.hidden) {
            return;
        }

        if (event.key === 'Escape') {
            close();
        }

        // Fokus im geöffneten Menü halten
        if (event.key === 'Tab') {
            const items = Array.from(focusables());
            const first = items[0];
            const last = items[items.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
        if (event.matches && !panel.hidden) {
            close(false);
        }
    });
}

function initHeaderState() {
    const header = document.querySelector('[data-site-header]');

    if (!header) {
        return;
    }

    const update = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
    update();
    window.addEventListener('scroll', update, { passive: true });
}

function initReveal() {
    const items = document.querySelectorAll('[data-reveal]');

    if (!items.length || !('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { rootMargin: '0px 0px -10% 0px' },
    );

    items.forEach((item) => {
        item.classList.add('reveal');
        observer.observe(item);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initMobileNav();
    initHeaderState();
    initReveal();
});
