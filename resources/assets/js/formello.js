document.addEventListener('DOMContentLoaded', function () {

    // IMask initialization
    const maskElements = document.querySelectorAll('[data-formello-mask]');
    maskElements.forEach(function (el) {
        try {
            const data = JSON.parse(el.getAttribute('data-formello-mask'));

            // Coerce special literals 'Number' and 'Date' to actual constructors for IMask
            let maskOptions = data;
            if (typeof maskOptions === 'string') {
                if (maskOptions === 'Number') {
                    maskOptions = { mask: Number };
                } else if (maskOptions === 'Date') {
                    maskOptions = { mask: Date };
                } else {
                    // Treat any other string as a pattern string
                    maskOptions = { mask: maskOptions };
                }
            } else if (maskOptions && typeof maskOptions === 'object') {
                if (typeof maskOptions.mask === 'string') {
                    if (maskOptions.mask === 'Number') {
                        maskOptions.mask = Number;
                    } else if (maskOptions.mask === 'Date') {
                        maskOptions.mask = Date;
                    }
                    // Any other string stays as-is
                }
            }

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
});
