# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


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
