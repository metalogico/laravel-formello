# Formello Widget Reference

This document lists the configuration parameters supported by each built-in widget. It reflects the current implementation in `src/Widgets/` and the Blade templates under `resources/views/widgets/`.

Since v2.0, fields are defined using the fluent `FormelloField` builder:

```php
use Metalogico\Formello\FormelloField;

FormelloField::make('name')
    ->label('Full Name')
    ->widget('text')
    ->help('Enter your full name')
    ->attributes(['placeholder' => 'John Doe'])
    ->required()
    ->columns(6);
```

### Common fluent methods

| Method | Description |
|--------|-------------|
| `->label(string)` | Field label |
| `->help(string)` | Help text below the field |
| `->widget(string, array $options)` | Widget type and widget-specific options |
| `->columns(int)` | Grid column span (1-12) |
| `->value(mixed)` | Explicit value (auto-resolved from model if omitted) |
| `->choices(array\|Closure)` | Options for select/radio/checkboxes |
| `->multiple(bool)` | Enable multiple selection |
| `->attributes(array)` | HTML attributes |
| `->required()` | Shortcut for `->attributes(['required' => true])` |
| `->readonly()` | Shortcut for `->attributes(['readonly' => true])` |
| `->disabled()` | Shortcut for `->attributes(['disabled' => true])` |
| `->type(string)` | Input type (e.g., `email`, `password`, `number`) |
| `->icon(string)` | Icon HTML for input group |
| `->format(string)` | Date format |
| `->reactive(array)` | Reactive config (see [reactive.md](reactive.md)) |
| `->extra(string, mixed)` | Arbitrary extra config key |

`->help()` and `->icon()` are rendered as raw HTML. Pass only trusted markup.

### Notes

- Widget-specific options passed via `->widget('type', $options)` are stored under the widget type key (e.g., `'date' => [...]`, `'tomselect' => [...]`)
- Frameworks: templates exist for `bootstrap5` and `tailwindcss4`. Rendering differs in classes only
- Assets: see `config/formello.php` under `assets` to enable/disable library loading

---

## Text (`TextWidget`)

```php
FormelloField::make('email')
    ->label('Email')
    ->type('email')
    ->icon('<i class="fa-solid fa-envelope"></i>')
```

- `->type()`: default `text`. Special handling:
  - `number` adds `inputmode=numeric` and `pattern=[0-9]*`
  - `email` adds `autocomplete=email`
  - `password` sets value to empty on render
- `->icon()`: renders inside an input group (Bootstrap/Tailwind)
- Supports `prefix`, `suffix` via `->extra('prefix', '...')`

## Textarea (`TextareaWidget`)

```php
FormelloField::make('description')
    ->label('Description')
    ->widget('textarea')
    ->attributes(['rows' => 5])
```

## Mask (`MaskWidget`)

Extends Text. Uses IMask.js for input masking.

```php
FormelloField::make('price')
    ->label('Price')
    ->widget('mask', ['mask' => 'Number', 'scale' => 2])

FormelloField::make('phone')
    ->label('Phone')
    ->widget('mask', ['mask' => '+00 000 000 0000'])
```

- Widget options are passed to IMask via `data-formello-mask`

## Hidden (`HiddenWidget`)

```php
FormelloField::make('token')
    ->widget('hidden')
    ->value('abc123')
```

## Select (`SelectWidget`)

```php
FormelloField::make('category_id')
    ->label('Category')
    ->widget('select')
    ->choices(fn () => Category::pluck('name', 'id')->toArray())
    ->multiple()
```

- `->choices()`: array or callable returning `[value => label]`
- `->multiple()`: when true, `name` becomes `name[]`

## TomSelect (`TomSelectWidget`)

### Static choices

```php
FormelloField::make('status')
    ->label('Status')
    ->widget('tomselect')
    ->choices(['active' => 'Active', 'inactive' => 'Inactive'])
```

### AJAX search

```php
FormelloField::make('category_id')
    ->label('Category')
    ->widget('tomselect', [
        'route' => route('categories.search'),
        'model' => Category::class,
        'label_field' => 'name',
        'value_field' => 'id',
        'placeholder' => 'Search categories...',
    ])
```

### Dependent selects

```php
FormelloField::make('region_id')
    ->label('Region')
    ->widget('tomselect', [
        'route' => route('regions.search'),
    ])

FormelloField::make('province_id')
    ->label('Province')
    ->widget('tomselect', [
        'route' => route('provinces.search'),
        'depends_on' => 'region_id',
        'depends_param' => 'region_id',
    ])
```

Widget options (`->widget('tomselect', [...])`):
- `route`: URL for AJAX search
- `model`: FQCN used to preload current value(s) into choices
- `label_field`: defaults to `name`
- `value_field`: defaults to `id`
- `placeholder`: default `"Select"`
- `dropdownParent`: CSS selector (default: `body`)
- `depends_on`: parent field id (child disabled until parent has value)
- `depends_param`: request param name sent to AJAX (defaults to `depends_on`)
- AJAX defaults: `minLength=0` (results load on open), `preload=focus`, `delay=250ms`

> **Tip:** For more complex dependent selects (e.g., cascading with DB queries), consider using the [reactive system](reactive.md) with `->reactive(['server' => 'onParentChanged'])`.

## Radio (`RadioWidget`)

```php
FormelloField::make('gender')
    ->label('Gender')
    ->widget('radio')
    ->choices(['m' => 'Male', 'f' => 'Female'])
```

- `->choices()`: array or callable returning `[value => label]`

## Checkboxes (`CheckboxesWidget`)

```php
FormelloField::make('tags')
    ->label('Tags')
    ->widget('checkboxes')
    ->choices(['php' => 'PHP', 'js' => 'JavaScript', 'go' => 'Go'])
    ->extra('select-all', ['enabled' => true, 'label' => 'Select all'])
```

- `->choices()`: array or callable returning `[value => label]`
- `select-all` (via `->extra()`): `enabled` (bool), `label` (string, default: "Select all / Unselect all")

## Toggle (`ToggleWidget`)

```php
FormelloField::make('in_stock')
    ->label('In Stock')
    ->widget('toggle')
```

- Renders as `type=checkbox` with `role=switch`
- If the value is truthy, `checked` is set

## Range (`RangeWidget`)

```php
FormelloField::make('rating')
    ->label('Rating')
    ->widget('range')
    ->attributes(['min' => 1, 'max' => 10, 'step' => 1])
```

- `min` (default 0), `max` (default 100), `step` (default 1)
- `showValue`: boolean (default true), display current value — via `->extra('showValue', false)`

## Date (`DateWidget`)

```php
FormelloField::make('publish_at')
    ->label('Publish at')
    ->widget('date', ['altFormat' => 'd/m/Y'])
    ->format('Y-m-d')
```

- `->format()`: PHP date format (default `Y-m-d`)
- Widget options are Flatpickr options, merged with defaults:
  - `altInput=true`, `altFormat='d F Y'`, `dateFormat='Y-m-d'`, `locale='it'`
- Supports `->icon()` for input group

## DateTime (`DateTimeWidget`)

Extends Date.

```php
FormelloField::make('event_at')
    ->label('Event date & time')
    ->widget('datetime')
```

- Inherits all Date options
- Different defaults: `altFormat='d F Y H:i'`, `dateFormat='Y-m-d H:i'`, `enableTime=true`, `time_24hr=true`
- `->format()` default: `Y-m-d H:i`

## Upload (`UploadWidget`)

```php
FormelloField::make('avatar')
    ->label('Avatar')
    ->widget('upload')
    ->attributes(['accept' => 'image/*'])
```

- Automatically sets `enctype="multipart/form-data"` on the form

## Color (`ColorWidget`)

```php
FormelloField::make('brand_color')
    ->label('Brand Color')
    ->widget('color')
```

- Uses Pickr nano library
- Widget options (via `->widget('color', [...])`): Pickr options merged with defaults
  - Defaults: `theme='nano'`, components (preview, opacity, hue, interaction with hex, rgba, input, clear, save)
- Supports `->icon()` for input group

## ColorSwatch (`ColorSwatchWidget`)

Extends Color. Swatches-only mode.

```php
FormelloField::make('theme_color')
    ->label('Theme Color')
    ->widget('colorswatch', [
        'swatches' => ['#FF0000', '#00FF00', '#0000FF'],
    ])
```

- Disables preview/opacity/hue and interaction controls
- Provides a default set of 20 swatches, overridable via widget options

## Wysiwyg (`WysiwygWidget`)

```php
FormelloField::make('content')
    ->label('Content')
    ->widget('wysiwyg', ['toolbarAdaptive' => false])
```

- Uses Jodit Editor (MIT, no CDN required)
- Widget options are Jodit options, applied via `data-formello-wysiwyg`
- Italian localization by default

## Separator (`SeparatorWidget`)

```php
FormelloField::make('section_divider')
    ->label('Additional Info')
    ->widget('separator')
    ->columns(12)
```

- Renders a visual divider between form sections
- `->label()` is optional — renders as heading text above the separator line

---

## Reactive System

Any field can be made reactive by adding `->reactive()`:

```php
FormelloField::make('status')
    ->widget('select')
    ->choices(['active' => 'Active', 'other' => 'Other...'])
    ->reactive(['client' => 'onStatusChanged'])
```

See [reactive.md](reactive.md) for full documentation.

---

## Assets and Framework Config

See `config/formello.php`:
- `css_framework`: `bootstrap5` or `tailwindcss4`
- `custom_widgets`: map alias to FQCN to override built-in widgets
- `assets`: enable/disable library loading per widget type
- `reactive`: configure compute endpoint and allowed forms whitelist

## Examples

Field definitions using the fluent builder:

```php
use Metalogico\Formello\FormelloField;

protected function fields(): array
{
    return [
        FormelloField::make('name')
            ->label('Name')
            ->help('Enter your full name')
            ->attributes(['placeholder' => 'John Doe'])
            ->required(),

        FormelloField::make('category_id')
            ->label('Category')
            ->widget('select')
            ->choices(fn () => Category::pluck('name', 'id')->toArray())
            ->multiple(),

        FormelloField::make('publish_at')
            ->label('Publish at')
            ->widget('date', ['altFormat' => 'd/m/Y'])
            ->format('Y-m-d'),

        FormelloField::make('content')
            ->label('Content')
            ->widget('wysiwyg', ['toolbarAdaptive' => false]),

        FormelloField::make('status')
            ->widget('select')
            ->choices(['active' => 'Active', 'other' => 'Other...'])
            ->reactive(['client' => 'onStatusChanged']),
    ];
}
```
