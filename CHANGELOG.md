# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.1.0] - 2026-02-09

### Added
- **Reactive System**: Callback-based field interdependency engine with two execution layers:
  - `reactive.client` — instant JS callbacks via `window.FormelloReactive`
  - `reactive.server` — async PHP callbacks via POST endpoint
- **FormelloState** (`src/Support/FormelloState.php`): State object with identical API in JS and PHP (`get`, `set`, `setOptions`, `setAttributes`)
- **FormelloComputeController** (`src/Http/Controllers/FormelloComputeController.php`): POST endpoint for server-side reactive callbacks
- **formello-reactive.js**: Client-side reactive engine with debounced input handling, widget-aware value setters (TomSelect, Flatpickr, Jodit), and initial state execution on page load
- **Reactive config section** in `config/formello.php` with `compute_path` and `allowed_forms` whitelist
- **Reactive data attributes** on `<form>` and field wrapper `<div>` elements for JS engine binding
- New methods on `Formello.php`: `getFieldConfig()`, `hasReactiveFields()`, `getReactiveMap()`
- Documentation: `docs/reactive.md`
- Unit tests for `FormelloState` and reactive methods (13 new tests)

## [2.0.0] - 2026-02-08

### Added
- **Tailwind CSS 4 widgets**
- **Tom Select Widget**: New `TomSelectWidget` using Tom Select for a modern, lightweight, and accessible select dropdown with features like search, pagination, and custom templates.
- **Dependent Select**: It's possible now to make a select field dependent on another select field (using the new tomselect widget).
- **Separator Widget**: New separator widget for dividing sections in your forms.
- **Custom Widgets Config**: config/formello.php now uses "custom_widgets" to alias your custom widgets.

### Warning: BREAKING CHANGES!
- **Select2 Widget**: The `Select2Widget` has been deprecated in favor of the `TomSelectWidget`.
- Removed dependencies on jQuery
- 'boolean' widget is now 'toggle'

### Fixed
- Date and DateTime: error messages now appear below the field and the red highlight works correctly, including with icons/prefix/suffix.
- Jodit WYSIWYG: removed safeMode so plugins load by default; fixed config binding.

## [1.2.6] - 2025-08-06

### Changed
- 'boolean' widget is now 'toggle'
- 'select2' widget now uses a custom data-formello-select2 trigger to avoid collisions

## [1.2.5] - 2025-08-01

### Changed
- README corrections for the custom widgets section.

### Added
- Added --name option to make command to create forms with a custom name.

## [1.2.4] - 2025-07-30

### Fixed
- Fixed an issue where the Select2 widget would incorrectly get the `multiple` attribute even when not specified in the field configuration. The logic now correctly passes the `multiple` state from the PHP backend to the JavaScript initialization via a `data-multiple` attribute, ensuring the widget behaves as expected.

## [1.2.2] - 2025-07-24

### Added
- **Select2 Bootstrap 5 Theme**: it's possible to define a theme for select2 using the 'theme' config option.


## [1.2.0] - 2025-07-24

### Added
- **WYSIWYG Editor Widget**: New `WysiwygWidget` using Jodit Editor for rich text editing with comprehensive features including tables, images, links, and formatting.
- **Dedicated Mask Widget**: New `MaskWidget` extending `TextWidget` specifically for input masking with IMask.js, providing better separation of concerns.
- **Enhanced Asset Management System**: Completely redesigned asset configuration system allowing users to disable specific widget libraries to prevent conflicts with existing theme assets.

### Changed
- **Improved Asset Configuration**: Asset configuration now uses widget names directly (`'wysiwyg' => false`) instead of library names, making it more intuitive and maintainable.
- **TextWidget Simplification**: Removed mask logic from `TextWidget` as it's now handled by the dedicated `MaskWidget`.
- **Streamlined Asset Loading**: Simplified asset registration logic with direct widget-to-config mapping, eliminating hardcoded mappings.

### Fixed
- **Asset Configuration Bug**: Fixed issue where setting libraries to `false` in config didn't prevent asset loading.
- **Widget Type Detection**: Improved widget type detection in asset management system for more reliable asset filtering.


## [1.1.0] - 2025-07-22

### Added
- **Icon Support for Text Fields**: Added a new `icon` option to the text widget to display an icon inside the input field using Bootstrap's input groups.
- **Input Masking for Text Fields**: Integrated IMask.js to add input masking capabilities. Added a new `mask` option to the text widget to define custom input masks.
- **Flatpickr Date/DateTime Widgets**: Replaced native HTML5 date inputs with Flatpickr for better UX and cross-browser compatibility.
- **Italian Localization**: Added Italian locale support for Flatpickr date/datetime pickers with proper month and day names.
- **Color Picker Widget**: New `ColorWidget` using Pickr nano library for full color selection with preview, opacity, and multiple format support.
- **Color Swatch Widget**: New `ColorSwatchWidget` for predefined color selection from customizable swatches, perfect for brand colors and design systems.
- **Enhanced Asset Management**: Added Pickr library assets (JS/CSS) with automatic publishing via ServiceProvider.

### Changed
- **DateTimeWidget Refactoring**: DateTimeWidget now extends DateWidget for better code reuse and consistency.
- **Template Optimization**: Eliminated duplicate datetime.blade.php template by reusing the date template.
- **Improved JavaScript Integration**: Enhanced formello.js with proper Pickr initialization and event handling.



## [1.0.0] - 2025-07-21

### Added

- Initial release of Laravel Formello.
- Automatic form generation from Eloquent models.
- Declarative form definition using simple PHP classes.
- Built-in support for Bootstrap 5 CSS framework.
- A comprehensive set of widgets: `Text`, `Textarea`, `Select`, `Select2`, `Radio`, `Checkboxes`, `Toggle`, `Range`, `Date`, `DateTime`, `Upload`, and `Hidden`.
- Automatic rendering of form fields, labels, help text, and validation errors.
- `isCreating()` and `isEditing()` methods for conditional logic within form classes.
- Support for custom, user-defined widgets.
- Artisan command `php artisan make:formello` to quickly scaffold form classes from models.
- Publishable configuration file for easy customization.
- Publishable views for full control over the rendered HTML.
- Unit and Feature tests to ensure reliability.

### Fixed

- Correctly detect form mode (`create` vs `edit`) based on the model's existence in the database (`$model->exists`).
