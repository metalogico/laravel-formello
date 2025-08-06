document.addEventListener('DOMContentLoaded', function () {

    // IMask initialization
    const maskElements = document.querySelectorAll('[data-formello-mask]');
    maskElements.forEach(function (el) {
        try {
            const maskOptions = JSON.parse(el.getAttribute('data-formello-mask'));
            IMask(el, maskOptions);
        } catch (e) {
            console.error('Error parsing Formello mask options:', e);
        }
    });

    // Flatpickr initialization
    const datepickerElements = document.querySelectorAll('[data-formello-datepicker]');
    datepickerElements.forEach(function (el) {
        try {
            const flatpickrOptions = JSON.parse(el.getAttribute('data-formello-datepicker'));
            flatpickr(el, flatpickrOptions);
        } catch (e) {
            console.error('Error parsing Formello datepicker options:', e);
        }
    });

    // Pickr color picker initialization
    const colorpickerElements = document.querySelectorAll('[data-formello-colorpicker]');
    colorpickerElements.forEach(function (el) {
        try {
            const pickrOptions = JSON.parse(el.getAttribute('data-formello-colorpicker'));

            // Create Pickr instance
            const pickr = Pickr.create({
                el: el,
                ...pickrOptions
            });

            // Update input value when color changes
            pickr.on('change', (color) => {
                el.value = color.toHEXA().toString();
                // Trigger change event for form validation
                el.dispatchEvent(new Event('change', { bubbles: true }));
            });

            // Handle swatch clicks for swatches-only mode
            pickr.on('swatchselect', (color) => {
                const hexColor = color.toHEXA().toString();
                el.value = hexColor;

                // Update the visual representation
                pickr.setColor(hexColor);

                // Trigger change event for form validation
                el.dispatchEvent(new Event('change', { bubbles: true }));

                // Close the picker automatically for swatches
                pickr.hide();
            });

        } catch (e) {
            console.error('Error parsing Formello colorpicker options:', e);
        }
    });

    // Jodit WYSIWYG initialization
    const wysiwygElements = document.querySelectorAll('[data-formello-wysiwyg]');
    if (wysiwygElements.length > 0 && typeof Jodit !== 'undefined') {
        wysiwygElements.forEach(function (el) {
            try {
                const joditOptions = JSON.parse(el.getAttribute('data-formello-wysiwyg'));

                // Default configuration - simple and stable
                const defaultConfig = {
                    minHeight: 300,
                    maxHeight: 300,
                    iframe: true,
                    toolbarSticky: false,
                    safeMode: true,
                    showCharsCounter: false,
                    showWordsCounter: false,
                    showXPathInStatusbar: false,
                    buttons: [
                        'bold',
                        'italic',
                        'underline',
                        '|',
                        'ul',
                        'ol',
                        '|',
                        'fontsize',
                        'brush',
                        '|',
                        'left',
                        'center',
                        'right',
                        '|',
                        'undo',
                        'redo',
                        '|',
                        'hr',
                        '|',
                        'fullsize'
                    ],
                    removeButtons: ['about', 'print'],
                };

                // Merge user options with defaults
                const finalConfig = { ...defaultConfig, ...joditOptions };

                // Initialize Jodit
                const editor = new Jodit(el, finalConfig);

                // Update textarea value when content changes
                editor.events.on('change', function () {
                    // Trigger change event for form validation
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                });

            } catch (e) {
                console.error('Error parsing Formello WYSIWYG options:', e);
            }
        });
    } else if (wysiwygElements.length > 0) {
        console.warn('Jodit not loaded but WYSIWYG elements found. Make sure to include Jodit script before formello.js');
    }

    // Select2 initialization
    const select2Elements = document.querySelectorAll('[data-formello-select2]');
    if (typeof $ !== 'undefined' && $.fn.select2) {
        select2Elements.forEach(function (el) {
            try {

                $(el).select2({
                    theme: 'bootstrap-5',
                });

            } catch (e) {
                console.error('Error initializing Formello Select2:', e);
            }
        });
    } else if (select2Elements.length > 0) {
        console.warn('Select2 not loaded but Select2 elements found. Make sure to include Select2 script and jQuery before formello.js');
    }
});
