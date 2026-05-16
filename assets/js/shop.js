// Shop page — filters (PHP) + mobile expand/collapse

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('shop-filters-form');
    if (!form) {
        return;
    }

    const sortSelect = document.getElementById('shop-sort');
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            form.submit();
        });
    }

    const autoInputs = form.querySelectorAll('.shop-filter-auto');
    autoInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            if (input.name === 'category[]' && input.value === 'all' && input.checked) {
                form.querySelectorAll('.shop-category-check').forEach(function (cb) {
                    cb.checked = false;
                });
            } else if (input.classList.contains('shop-category-check') && input.checked) {
                const allCb = form.querySelector('#cat-all');
                if (allCb) {
                    allCb.checked = false;
                }
            }
            form.submit();
        });
    });

    // Main filters panel (mobile)
    const filtersToggle = document.getElementById('shop-filters-toggle');
    const filtersPanel = document.getElementById('shop-filters-panel');

    if (filtersToggle && filtersPanel) {
        filtersToggle.addEventListener('click', function () {
            const isOpen = filtersPanel.classList.toggle('is-open');
            filtersToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    // Section accordions (mobile only)
    const accordionTriggers = document.querySelectorAll('[data-filter-accordion]');

    function syncAccordionBody(trigger) {
        const expanded = trigger.getAttribute('aria-expanded') === 'true';
        let body = trigger.nextElementSibling;

        if (body && body.classList.contains('filter-title')) {
            body = body.nextElementSibling;
        }

        if (body && body.classList.contains('filter-accordion__body')) {
            body.classList.toggle('is-open', expanded);
        }
    }

    accordionTriggers.forEach(function (trigger) {
        syncAccordionBody(trigger);

        trigger.addEventListener('click', function () {
            const expanded = trigger.getAttribute('aria-expanded') === 'true';
            trigger.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            syncAccordionBody(trigger);
        });
    });

    // Open accordion bodies on desktop resize
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) {
            accordionTriggers.forEach(function (trigger) {
                trigger.setAttribute('aria-expanded', 'true');
                syncAccordionBody(trigger);
            });
        }
    });
});
