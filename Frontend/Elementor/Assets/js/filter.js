/**
 * BlogKit - category filter tabs and live search for post widgets.
 * Requests the same page with the filter in the query string, then swaps the
 * results (everything after the filter bar) of the matching widget.
 */
(function () {
    'use strict';

    if (document.body && document.body.classList.contains('elementor-editor-active')) {
        return;
    }

    var timers = new WeakMap();
    var controllers = new WeakMap();

    function widgetOf(el) {
        return el.closest('.elementor-element[data-id]');
    }

    function request(bar) {
        var widget = widgetOf(bar);
        if (!widget) { return; }

        var url = new URL(window.location.href);
        url.searchParams.delete('paged');
        url.searchParams.delete(bar.getAttribute('data-cat-param'));
        url.searchParams.delete(bar.getAttribute('data-search-param'));

        var cat = bar.getAttribute('data-cat');
        var input = bar.querySelector('.blogkit-filter-search');
        var term = input ? input.value.trim() : '';
        if (cat) { url.searchParams.set(bar.getAttribute('data-cat-param'), cat); }
        if (term) { url.searchParams.set(bar.getAttribute('data-search-param'), term); }

        if (controllers.has(bar)) { controllers.get(bar).abort(); }
        var controller = 'AbortController' in window ? new AbortController() : null;
        if (controller) { controllers.set(bar, controller); }

        widget.classList.add('is-filtering');

        fetch(url.toString(), { credentials: 'same-origin', signal: controller ? controller.signal : undefined })
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
                var remoteBar = remote && remote.querySelector('.blogkit-filter-bar');
                if (!remoteBar) { throw new Error('no bar'); }

                // Remove the current results, then append the new ones.
                while (bar.nextSibling) { bar.parentNode.removeChild(bar.nextSibling); }
                var node = remoteBar.nextSibling;
                while (node) {
                    var next = node.nextSibling;
                    bar.parentNode.appendChild(document.adoptNode(node));
                    node = next;
                }
                widget.classList.remove('is-filtering');
                if (typeof window.blogkitScanLoadMore === 'function') { window.blogkitScanLoadMore(widget); }
            })
            .catch(function (error) {
                if (error && error.name === 'AbortError') { return; }
                widget.classList.remove('is-filtering');
                window.location.href = url.toString();
            });
    }

    document.addEventListener('click', function (event) {
        var tab = event.target.closest ? event.target.closest('.blogkit-filter-tab') : null;
        if (!tab) { return; }
        var bar = tab.closest('.blogkit-filter-bar');
        if (!bar) { return; }
        event.preventDefault();
        bar.setAttribute('data-cat', tab.getAttribute('data-slug') || '');
        bar.querySelectorAll('.blogkit-filter-tab').forEach(function (el) {
            el.classList.toggle('is-active', el === tab);
        });
        request(bar);
    });

    document.addEventListener('input', function (event) {
        var input = event.target;
        if (!input.classList || !input.classList.contains('blogkit-filter-search')) { return; }
        var bar = input.closest('.blogkit-filter-bar');
        if (!bar) { return; }
        clearTimeout(timers.get(bar));
        timers.set(bar, setTimeout(function () { request(bar); }, 350));
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && event.target.classList && event.target.classList.contains('blogkit-filter-search')) {
            event.preventDefault();
        }
    });
})();
