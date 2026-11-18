/**
 * BlogKit - Post Carousel (Swiper).
 */
(function () {
    'use strict';

    function init(root) {
        if (typeof Swiper === 'undefined') { return; }
        (root || document).querySelectorAll('.blogkit-post-carousel').forEach(function (el) {
            if (el.swiper || el.getAttribute('data-bk-init')) { return; }
            var cfg;
            try { cfg = JSON.parse(el.getAttribute('data-config') || '{}'); } catch (e) { return; }
            var prev = el.getAttribute('data-prev');
            var next = el.getAttribute('data-next');
            var dots = el.getAttribute('data-dots');

            el.setAttribute('data-bk-init', '1');
            new Swiper(el, {
                loop: !!cfg.loop,
                grabCursor: true,
                watchOverflow: true,
                autoplay: cfg.autoplay ? { delay: cfg.autoplay, pauseOnMouseEnter: true, disableOnInteraction: false } : false,
                navigation: document.querySelector(prev) ? { prevEl: prev, nextEl: next } : false,
                pagination: document.querySelector(dots) ? { el: dots, clickable: true } : false,
                slidesPerView: cfg.perView.mobile,
                spaceBetween: cfg.space.mobile,
                breakpoints: {
                    768: { slidesPerView: cfg.perView.tablet, spaceBetween: cfg.space.tablet },
                    1025: { slidesPerView: cfg.perView.desktop, spaceBetween: cfg.space.desktop }
                }
            });
        });
    }

    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', function () { init(); });
    } else {
        init();
    }

    window.addEventListener('elementor/frontend/init', function () {
        if (window.elementorFrontend && elementorFrontend.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/blogkit-post-carousel.default', function ($scope) {
                init($scope[0]);
            });
        }
    });
})();
