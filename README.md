![image](https://github.com/user-attachments/assets/5f54ca42-1aa9-44a5-92b0-79862d4f3f27)

# Formello

A Laravel package for generating Bootstrap 5 and Tailwind CSS 4 forms based on models. Laravel 11+

Formello is a comprehensive form generation and handling tool for Laravel applications, inspired by Django forms.

<img width="1255" height="887" alt="SCR-20250730-kida" src="https://github.com/user-attachments/assets/dbc897c6-2d1d-40f7-8d3f-14bed9e8868a" />

## 🎉 Motivation

The Laravel ecosystem offers powerful tools for building applications, from full-featured admin panels like Nova and Filament to complex form-handling libraries. However, I felt there was a need for a tool that sits in the "sweet spot" between these solutions.

Formello was created for developers who need to generate forms quickly without the overhead of a complete admin panel, but who also want a simpler, more intuitive API than more complex form libraries. It's designed to automate the repetitive aspects of form creation while giving you full control over the final output.

Currently, Formello ships with built-in support for **Bootstrap 5** and **Tailwind CSS 4**.

If you use this project, please consider giving it a ⭐.

## ✨ Features

- **Fluent field builder** with `FormelloField::make()` API
- **Reactive system** for field interdependencies (client-side JS and/or server-side PHP callbacks)
- Automatic form rendering with **Bootstrap 5** and **Tailwind CSS 4**
- Rich set of built-in widgets:
  - **Text**, **Textarea**, **Hidden**
  - **Select** (with multiple), **TomSelect** (AJAX, search, dependent selects)
  - **Radio**, **Checkboxes**, **Toggle**
  - **Range**
  - **Date**, **DateTime** (Flatpickr with Italian localization)
  - **Mask** (IMask.js input masking)
  - **Color**, **ColorSwatch** (Pickr nano)
  - **Wysiwyg** (Jodit Editor)
  - **Upload**
  - **Separator**
- Customizable and extensible widgets
- Modular asset management (disable libraries your theme already includes)
- Automatic error handling and display
- Artisan scaffolding command

## ⚠️ Upgrading to v2.x

Versions 2.0 and 2.1 introduce **breaking changes** from the 1.x series.

### Breaking changes in v2.0

- **Fluent field builder**: `fields()` now returns `FormelloField[]` instead of associative arrays
- **Select2 removed**: use `TomSelectWidget` instead. `HasSelect2Widget` trait replaced by `HasTomSelectWidget`
- **jQuery removed**: no longer a dependency
- **Widget rename**: `'boolean'` is now `'toggle'`
- **Tailwind CSS 4**: full widget support added

### New in v2.1

- **Reactive System**: callback-based field interdependencies with `reactive.client` (JS) and `reactive.server` (PHP)

### Migration from v1.x

```php
// Before (v1.x)
protected function fields(): array
{
    return [
        'name' => [
            'label' => 'Name',
            'widget' => 'text',
        ],
    ];
}

// After (v2.x)
use Metalogico\Formello\FormelloField;

protected function fields(): array
{
    return [
        FormelloField::make('name')->label('Name')->widget('text'),
    ];
}
```

**After upgrading, republish the assets:**

```bash
php artisan vendor:publish --tag=formello-assets --force
php artisan vendor:publish --tag=formello-config --force
```

## 🛠️ Installation

1. Install the package via Composer:

```bash
composer require metalogico/laravel-formello
```

2. Publish the assets:

```bash
php artisan vendor:publish --tag=formello-assets
```

3. (Optional) Auto-publish assets on update

To ensure that Formello's assets are automatically updated every time you run `composer update`, you can add a command to the `post-update-cmd` script in your project's `composer.json` file.

```json
"scripts": {
    "post-update-cmd": [
        "@php artisan vendor:publish --tag=formello-assets --force"
    ]
}
```

## 😎 How to use

### Creating a Form

Create a new form class that extends `Metalogico\Formello\Formello`. Fields are defined using the fluent `FormelloField` builder.

```php
<?php

namespace App\Forms;

use App\Models\Category;
use Metalogico\Formello\Formello;
use Metalogico\Formello\FormelloField;

class ProductForm extends Formello
{
    protected function create(): array
    {
        return [
            'method' => 'POST',
            'action' => route('products.store'),
        ];
    }

    protected function edit(): array
    {
        return [
            'method' => 'PATCH',
            'action' => route('products.update', $this->model->id),
        ];
    }

    protected function fields(): array
    {
        return [
            FormelloField::make('name')
                ->label(__('Product Name'))
                ->help('Enter the name of the product')
                ->required(),

            FormelloField::make('description')
                ->label(__('Description'))
                ->widget('textarea'),

            FormelloField::make('category_id')
                ->label(__('Category'))
                ->widget('select')
                ->choices(Category::pluck('name', 'id')->toArray()),

            FormelloField::make('price')
                ->label(__('Price'))
                ->widget('mask', ['mask' => 'Number', 'scale' => 2])
                ->columns(6),

            FormelloField::make('in_stock')
                ->label(__('In Stock'))
                ->widget('toggle')
                ->columns(6),
        ];
    }
}
```

Remember to add these fields to your model's `$fillable` array otherwise Formello will not render them.

```php
class Product extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'description',
        'price',
        'in_stock',
    ];
}
```

### Using the artisan command

You can generate a basic formello file using this command:

```bash
php artisan make:formello --model=Product
```

The script will generate a skeleton file that contains a basic field definition for each fillable field found in your model.

### Rendering the Form

In your controller for an empty form (create action):

```php
public function create()
{
    $formello = new ProductForm(Product::class);

    return view('products.create', [
        'formello' => $formello,
    ]);
}
```

or, for an edit form:

```php
public function edit(Product $product)
{
    $formello = new ProductForm($product);

    return view('products.edit', [
        'formello' => $formello,
    ]);
}
```

Then in your blade template:

```blade
{!! $formello->render() !!}
```

If you want to render only the fields (without the `<form>` tag) you can use:

```blade
@foreach ($formello->getFields() as $name => $field)
    {!! $formello->renderField($name) !!}
@endforeach
```

### Conditional Logic

You can use `isCreating()` and `isEditing()` methods in your form class to dynamically change fields based on the form's mode:

```php
protected function fields(): array
{
    $password_field = FormelloField::make('password')
        ->label(__('Password'))
        ->type('password');

    if ($this->isCreating()) {
        $password_field->required();
    }

    if ($this->isEditing()) {
        $password_field->help('Leave empty to keep the current password');
    }

    return [
        FormelloField::make('name')
            ->label(__('User Name'))
            ->help('Enter the name of the user'),

        $password_field,
    ];
}
```

### CSS Framework Override

By default, Formello uses the framework set in `config/formello.php`. You can override it per form:

```php
$formello = new ProductForm($product);
$formello->setCssFramework('tailwindcss4');
```

## ⚡ Reactive System

The reactive system enables field interdependencies without writing custom JavaScript from scratch. Add `->reactive()` to any field to declare callbacks.

### Client-side (instant)

```php
protected function fields(): array
{
    return [
        FormelloField::make('status')
            ->widget('select')
            ->choices(['active' => 'Active', 'other' => 'Other...'])
            ->reactive(['client' => 'onStatusChanged']),

        FormelloField::make('status_other')
            ->label('Specify'),
    ];
}
```

```js
window.FormelloReactive = {
    onStatusChanged(state) {
        const isOther = state.get('status') === 'other';
        state.setAttributes('status_other', {
            hidden: !isOther,
            required: isOther,
        });
        if (!isOther) state.set('status_other', '');
    },
};
```

### Server-side (async PHP)

For operations that need database access (e.g., dependent selects):

```php
FormelloField::make('region_id')
    ->widget('tomselect')
    ->reactive(['server' => 'onRegionChanged']),

FormelloField::make('province_id')
    ->widget('tomselect'),
```

```php
use Metalogico\Formello\Support\FormelloState;

public function onRegionChanged(FormelloState $state): void
{
    $region_id = $state->get('region_id');
    $provinces = Province::where('region_id', $region_id)
        ->pluck('name', 'id')->toArray();

    $state->setOptions('province_id', $provinces);
    $state->set('province_id', null);
}
```

> **Note:** Server-side reactive requires a `<meta name="csrf-token" content="{{ csrf_token() }}">` tag in your layout.

For full documentation see [docs/reactive.md](docs/reactive.md).

## Creating Custom Widgets

Formello is designed to be extensible. To create a custom widget:

### 1. Publish the config file

```bash
php artisan vendor:publish --tag=formello-config
```

Add your widget alias to the `custom_widgets` array:

```php
'custom_widgets' => [
    'star-rating' => App\Widgets\StarRatingWidget::class,
],
```

### 2. Create the Widget Class

Create a class extending `Metalogico\Formello\Widgets\BaseWidget`:

```php
<?php

namespace App\Widgets;

use Metalogico\Formello\Widgets\BaseWidget;

class StarRatingWidget extends BaseWidget
{
    public function getViewData(string $name, $value, array $config, array $errors): array
    {
        return [
            'name' => $name,
            'value' => $value,
            'config' => $config,
            'errors' => $errors,
        ];
    }

    public function getTemplate(): string
    {
        return 'widgets.star-rating';
    }
}
```

### 3. Create the Blade Template

Create `resources/views/widgets/star-rating.blade.php`:

```blade
<div class="mb-3">
    <label for="{{ $name }}" class="form-label">{{ $config['label'] }}</label>
    <input type="number"
           name="{{ $name }}"
           id="{{ $name }}"
           value="{{ $value }}"
           class="form-control @if ($errors) is-invalid @endif"
           min="1"
           max="5" />

    @if ($errors)
        <div class="invalid-feedback">
            @foreach ($errors as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif
</div>
```

### 4. Use it in your form

```php
FormelloField::make('rating')
    ->label(__('Product Rating'))
    ->widget('star-rating'),
```

Custom widgets get the reactive system for free — just add `->reactive()` to the field config.

## 🎨 Asset Management

Formello uses a modular system for loading JS and CSS assets, avoiding conflicts with themes that already include the same libraries.

### Blade Directives

Add these directives to your layout:

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Your App</title>

    <!-- Your existing CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Formello CSS - loads only what's needed -->
    @formelloStyles
</head>
<body>
    <!-- Your content -->

    <!-- Your existing JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Formello JS - loads only what's needed -->
    @formelloScripts
</body>
</html>
```

### Disabling Libraries

If your theme already includes a library, disable it in `config/formello.php`:

```php
'assets' => [
    'tomselect' => false, // theme already has Tom Select
    'date' => true,
    'datetime' => true,
    'mask' => true,
    'color' => true,
    'colorswatch' => true,
    'wysiwyg' => true,
],
```

### Supported Libraries

| Widget | Library | Assets |
|--------|---------|--------|
| TomSelect | Tom Select | tom-select.complete.js, tom-select.default.min.css |
| Date, DateTime | Flatpickr | flatpickr.min.js, l10n/it.js, flatpickr.min.css |
| Mask | IMask | imask.min.js |
| Color, ColorSwatch | Pickr | pickr.min.js, nano.min.css |
| Wysiwyg | Jodit | jodit.min.js, jodit.min.css |

## 📚 Documentation

- [Widget configuration reference](docs/widgets.md)
- [Reactive system](docs/reactive.md)

## ⚖️ License

Laravel Formello is open-sourced software licensed under the [MIT license](LICENSE.md).

## 🍺 Donations

If you really like this project and you want to help me please consider [buying me a beer 🍺](https://www.buymeacoffee.com/metalogico).
