document.addEventListener('DOMContentLoaded', function () {
    // IMask initialization
    const maskElements = document.querySelectorAll('[data-formello-mask]');
    maskElements.forEach(function(el) {
        try {
            const maskOptions = JSON.parse(el.getAttribute('data-formello-mask'));
            IMask(el, maskOptions);
        } catch (e) {
            console.error('Error parsing Formello mask options:', e);
        }
    });

    // Flatpickr initialization
    const datepickerElements = document.querySelectorAll('[data-formello-datepicker]');
    datepickerElements.forEach(function(el) {
        try {
            const flatpickrOptions = JSON.parse(el.getAttribute('data-formello-datepicker'));
            flatpickr(el, flatpickrOptions);
        } catch (e) {
            console.error('Error parsing Formello datepicker options:', e);
        }
    });

    // Pickr color picker initialization
    const colorpickerElements = document.querySelectorAll('[data-formello-colorpicker]');
    colorpickerElements.forEach(function(el) {
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
});
