document.addEventListener('DOMContentLoaded', function () {
    const elements = document.querySelectorAll('[data-formello-mask]');
    elements.forEach(function (el) {
        try {
            const options = JSON.parse(el.dataset.formelloMask);
            IMask(el, options);
        } catch (e) {
            console.error('Error parsing Formello mask options:', e, el.dataset.formelloMask);
        }
    });
});
