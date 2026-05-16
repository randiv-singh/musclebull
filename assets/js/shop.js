// Shop page — auto-submit filters (PHP handles filtering)

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
});
