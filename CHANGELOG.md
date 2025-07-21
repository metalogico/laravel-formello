# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
