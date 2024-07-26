# Formello

A Laravel package for generating Bootstrap 5 forms based on models. Laravel 9+

Formello is a comprehensive form generation and handling tool for Laravel applications, inspired by Django forms.

## 🎉 Motivation

Cross contamination again! After working with Django's powerful form system, I found myself missing a similar tool in the Laravel ecosystem. So, I decided to create Formello to bring that ease of use and flexibility to Laravel developers.

Formello is built using:
- [Laravel](https://laravel.com/)
- [Bootstrap 5](https://getbootstrap.com/)

If you use this project, please consider giving it a ⭐.

## ✨ Features

- Easy form definition using Laravel classes
- Automatic form rendering
- Support for various field types:
  - Text
  - Textarea
  - Select
  - Radio
  - Toggle
  - Date
  - DateTime
  - And more!
- Customizable widgets
- Automatic error handling and display
- Form validation integration

## 🛠️ How to install 

1. Install the package via Composer:

```bash
composer require metalogico/laravel-formello
```

## 😎 How to use

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
    protected function form(): array
    {
        return [
            'method' => 'POST',
            'action' => route('products.store'),
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

The script will generate a skeleton file that contains a basic field definition 
for each fillable field found in your model.


## Rendering the Form

In your controller for the create:

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

or, for the edit:

```php
public function edit(string $id)
{
    // pass the model to the form
    $category = Product::findOrFail($id);
    $formello = new TestForm($category);
    // pass it to the view
    return view('products.edit', [
        'formello' => $formello
    ]);
}
```

Then in you blade template:

```php
{{ $formello->render() }}
```


## ⚖️ License

DjangoSonar is open-sourced software licensed under the [MIT license](LICENSE.md).


## 🍺 Donations
If you really like this project and you want to help me please consider [buying me a beer 🍺](https://www.buymeacoffee.com/metalogico
) 