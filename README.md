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


## ⚖️ License

Laravel Formello is open-sourced software licensed under the [MIT license](LICENSE.md).


## 🍺 Donations
If you really like this project and you want to help me please consider [buying me a beer 🍺](https://www.buymeacoffee.com/metalogico
) 