/**
 * BlogKit - "Load more" / infinite scroll for post widgets.
 * Fetches the next page (a normal ?paged=N URL of the same site), takes the
 * items of the same widget from the response and appends them.
 */
(function () {
    'use strict';

    if (document.body && document.body.classList.contains('elementor-editor-active')) {
        return;
    }

    var observer = null;

    function sameOrigin(url) {
        try {
            return new URL(url, window.location.href).origin === window.location.origin;
        } catch (e) {
            return false;
        }
    }

    function observe(wrap) {
        if (!wrap || wrap.getAttribute('data-type') !== 'infinite' || !('IntersectionObserver' in window)) {
            return;
        }
        if (!observer) {
            observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        observer.unobserve(entry.target);
                        load(entry.target);
                    }
                });
            }, { rootMargin: '300px' });
        }
        observer.observe(wrap);
    }

    function load(wrap) {
        var link = wrap.querySelector('.blogkit-load-more');
        var widget = wrap.closest('.elementor-element[data-id]');
        var items = wrap.previousElementSibling;

        if (!link || !widget || !items || wrap.classList.contains('is-loading') || !sameOrigin(link.href)) {
            return;
        }

        wrap.classList.add('is-loading');
        link.setAttribute('aria-busy', 'true');

        fetch(link.href, { credentials: 'same-origin' })
            .then(function (response) {
                if (!response.ok) { throw new Error('bad response'); }
                return response.text();
            })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var remote = null;
                doc.querySelectorAll('.elementor-element[data-id]').forEach(function (el) {
                    if (el.getAttribute('data-id') === widget.getAttribute('data-id')) { remote = el; }
                });
                var remoteWrap = remote && remote.querySelector('.blogkit-load-more-wrap');
                var remoteItems = remoteWrap ? remoteWrap.previousElementSibling : (remote && remote.querySelector('.blogkit-pagination') ? remote.querySelector('.blogkit-pagination').previousElementSibling : null);
                if (!remoteItems) { throw new Error('no items'); }

                Array.prototype.slice.call(remoteItems.children).forEach(function (child) {
                    items.appendChild(document.adoptNode(child));
                });

                if (remoteWrap && remoteWrap.querySelector('.blogkit-load-more')) {
                    var adopted = document.adoptNode(remoteWrap);
                    wrap.parentNode.replaceChild(adopted, wrap);
                    observe(adopted);
                } else {
                    wrap.parentNode.removeChild(wrap);
                }
            })
            .catch(function () {
                // Fall back to normal navigation.
                wrap.classList.remove('is-loading');
                link.removeAttribute('aria-busy');
                window.location.href = link.href;
            });
    }

    document.addEventListener('click', function (event) {
        var link = event.target.closest ? event.target.closest('.blogkit-load-more') : null;
        if (!link) { return; }
        event.preventDefault();
        load(link.closest('.blogkit-load-more-wrap'));
    });

    function init() {
        document.querySelectorAll('.blogkit-load-more-wrap[data-type="infinite"]').forEach(observe);
    }

    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
