![image](https://github.com/user-attachments/assets/5f54ca42-1aa9-44a5-92b0-79862d4f3f27)

# Formello

A Laravel package for generating Bootstrap 5 forms based on models. Laravel 9+

Formello is a comprehensive form generation and handling tool for Laravel applications, inspired by Django forms.

## 🎉 Motivation

The Laravel ecosystem offers powerful tools for building applications, from full-featured admin panels like Nova and Filament to complex form-handling libraries. However, I felt there was a need for a tool that sits in the "sweet spot" between these solutions.

Formello was created for developers who need to generate forms quickly without the overhead of a complete admin panel, but who also want a simpler, more intuitive API than more complex form libraries. It's designed to automate the repetitive aspects of form creation while giving you full control over the final output.

Currently, Formello ships with built-in support for **Bootstrap 5**, and support for **Tailwind CSS** is coming soon™!

If you use this project, please consider giving it a ⭐.

## ✨ Features

- Easy form definition using Laravel classes
- Automatic form rendering
- Support for various field types:
  - Text
  - Textarea
  - Select (with multiple)
  - Select2
  - Radio
  - Checkboxes
  - Toggle
  - Range
  - Date
  - DateTime
  - Upload
  - Hidden
- Customizable widgets
- Automatic error handling and display
- Form validation integration

## 🛠️ How to install 

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

This will overwrite the existing assets with the latest ones from the package.


## 😎 How to use

Creating a Form
Create a new form class that extends `Metalogico\Formello\Formello`.

Here's a simple example for a product form.

```php
<?php

namespace App\Forms;

use Metalogico\Formello\Formello;
use Metalogico\Formello\Widgets\SelectWidget;

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
            'method' => 'POST',
            'action' => route('products.update', $this->model->id),
        ];
    }    

    protected function fields(): array
    {
        return [
            'name' => [
                'label' => __('Product Name'),
                'help' => 'Enter the name of the product',
            ],
            'description' => [
                'label' => __('Description'),
            ],
            'category_id' => [
                'label' => __('Category'),
                'widget' => SelectWidget::class,
                'choices' => function () {
                    return Category::pluck('name', 'id');
                },
            ],
            'in_stock' => [
              'label' => __('In Stock'),
            ],
        ];
    }
}
```

Remember to add these fields to your model's `$fillable` array otherwise Formello will not render them.

```php

class Product extends Model
{
    // ...
    protected $fillable = [
        'name',
        'category_id',
        'description',
        'in_stock',
    ];

}
```

## Using the provided artisan command

You can generate a basic formello file using this command:

```bash
php artisan make:formello --model=Product
```

The script will generate a skeleton file that contains a basic field definition for each fillable field found in your model.


## Rendering the Form

In your controller for an empty form (create action):

```php
public function create()
{
    // create the form
    $formello = new ProductForm(new Product);
    // pass it to the view
    return view('products.create', [
      'formello' => $formello
    ]);
}
```

or, for an edit form:

```php
public function edit(string $id)
{
    // pass the model to the form
    $category = Product::findOrFail($id);
    $formello = new ProductForm($category);
    // pass it to the view
    return view('products.edit', [
        'formello' => $formello
    ]);
}
```

## Conditional Logic

You can use `isCreating()` and `isEditing()` methods in your form class to dynamically change fields, labels, rules, or other options based on the form's mode.

Here's an example of how to use these methods to change a field's behavior:

```php
protected function fields(): array
{
    $fields = [
        'name' => [
            'label' => __('User Name'),
            'help' => 'Enter the name of the user',
        ],
        'password' => [
            'label' => __('Password'),
            'type' => 'password',
            'required' => $this->isCreating(),
            'help' => $this->isEditing() ? 'Leave the field empty to keep the current password' : '',
        ],
    ];

    return $fields;
}
```

Then in you blade template:

```php
{!! $formello->render() !!}
```

If you want to render only the fields (without the \<form\> tag) you can use:

```php
@foreach ($formello->getFields() as $name => $field)
    {!! $formello->renderField($name) !!}
@endforeach
```


## Creating Custom Widgets

Formello is designed to be extensible, allowing you to create your own custom widgets. This is useful when you need a specific form control that isn't included in the default set.

To create a custom widget, you need to follow these steps:

### 1. Create a Widget Class

First, create a new PHP class for your widget. This class must implement the `Metalogico\Formello\Interfaces\WidgetInterface`. This interface requires you to implement a single `render` method.

You can place this class anywhere in your project, for example, in `app/Widgets`.

To maintain a clean separation of concerns, the `render` method should delegate the rendering to a Blade template.

Here is an example of a `StarRatingWidget` class:

```php
<?php

namespace App\Widgets;

use Metalogico\Formello\Interfaces\WidgetInterface;

class StarRatingWidget implements WidgetInterface
{
    public function render(string $name, $value, array $config, array $errors): string
    {
        return view('widgets.star-rating', [
            'name' => $name,
            'value' => $value,
            'config' => $config,
            'errors' => $errors,
        ])->render();
    }
}
```

### 2. Create the Widget's Blade Template

Next, create the Blade template that will render the widget's HTML. For instance, you can create the file `resources/views/widgets/star-rating.blade.php`:

```blade
<div class="mb-3">
    <label for="{{ $name }}" class="form-label">{{ $config['label'] }}</label>
    <input type="number" 
           name="{{ $name }}" 
           id="{{ $name }}" 
           value="{{ $value }}" 
           class="form-control @if ($errors) is-invalid @endif" 
           min="1" 
           max="5"/>

    @if ($errors)
        <div class="invalid-feedback">
            <ul>
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
```

### 3. Use the Custom Widget in Your Form

Once you have created your widget class, you can use it in your Formello form by specifying the fully qualified class name in the `widget` option for a field.

```php
<?php

namespace App\Forms;

use Metalogico\Formello\Formello;
use App\Widgets\StarRatingWidget; // Import your custom widget

class ProductForm extends Formello
{
    // ... create() and edit() methods
    
    protected function fields(): array
    {
        return [
            // ... other fields
            'rating' => [
                'label' => __('Product Rating'),
                'widget' => StarRatingWidget::class,
                'help' => 'Rate the product from 1 to 5 stars.'
            ],
        ];
    }
}
```

Formello will automatically instantiate your widget class and call its `render` method to generate the HTML for the form field.

## 🎨 Asset Management - Modular System

Formello uses a modular and flexible system for loading JavaScript and CSS assets, avoiding conflicts with themes that already include the same libraries.

### How It Works

#### 1. Widget-Based Asset Management

Each widget defines its own assets through the `getAssets()` method (optional):

```php
public function getAssets(?array $fieldConfig = null): ?array
{
    return [
        'scripts' => ['flatpickr.min.js'],
        'styles' => ['flatpickr.min.css'],
    ];
}
```

#### 2. Blade Directives

Use the new blade directives in your layout:

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
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

#### 3. Custom Widgets

Custom widgets can optionally implement `getAssets()`:

```php
class CustomWidget extends BaseWidget
{
    // The getAssets() method is OPTIONAL
    public function getAssets(?array $fieldConfig = null): ?array
    {
        return [
            'scripts' => ['my-custom-lib.js'],
            'styles' => ['my-custom-styles.css'],
        ];
    }
    
    // If you don't implement getAssets(), the widget still works
}
```

### Supported Libraries

| Library | Widgets that use it | Assets loaded |
|----------|-------------------|----------------|
| `select2` | SelectWidget, Select2Widget | select2.min.js, select2.min.css, select2-bootstrap-5-theme.min.css |
| `flatpickr` | DateWidget, DateTimeWidget | flatpickr.min.js, l10n/it.js, flatpickr.min.css |
| `imask` | TextWidget (with mask) | imask.min.js |
| `pickr` | ColorWidget, ColorSwatchWidget | pickr.min.js, nano.min.css |
| `jodit` | WysiwygWidget | jodit.min.js, jodit.min.css |


## ⚖️ License

Laravel Formello is open-sourced software licensed under the [MIT license](LICENSE.md).


## 🍺 Donations
If you really like this project and you want to help me please consider [buying me a beer 🍺](https://www.buymeacoffee.com/metalogico
) 
