# Formello Widget Reference

This document lists the configuration parameters supported by each built-in widget. It reflects the current implementation in `src/Widgets/` and the Blade templates under `resources/views/widgets/`.

Notes
- Common keys: `label`, `help`, `attributes` (HTML attributes), `class` (inside `attributes`), `id` (inside `attributes`).
- Choices/options can be arrays or callables returning arrays.
- Some inputs support `prefix`, `suffix`, and `icon` for grouped inputs in templates.
- Frameworks: templates exist for `bootstrap5` and `tailwindcss4`. Rendering differs in classes only.
- Assets: see `config/formello.php` under `assets` to enable/disable library loading.

---

## Text (`TextWidget`)
Keys
- `label`, `help`
- `attributes`: supports `type` (default `text`). Special handling:
  - `number` adds `inputmode=numeric` and `pattern=[0-9]*`
  - `email` adds `autocomplete=email`
  - `password` sets value to empty on render
- `prefix`, `suffix`, `icon` (for input group templates)

## Textarea (`TextareaWidget`)
Keys
- `label`, `help`
- `attributes`: includes `id`, `class`. Rendered as `<textarea>`

## Mask (`MaskWidget`)
Extends Text.
Keys
- All Text keys
- `mask`: array/object passed to `data-formello-mask` for IMask

## Hidden (`HiddenWidget`)
Keys
- No special keys. Renders hidden field with `name` and `value`.

## Select (`SelectWidget`)
Keys
- `label`, `help`
- `choices`: array or callable returning `[value => label]`
- `multiple`: boolean. When true, `name` becomes `name[]` and `multiple` attribute is set
- `attributes`: standard HTML attributes (e.g., `id`, `class`)

## Select2 (`Select2Widget`) - Deprecated
Use "tomselect" instead.

## TomSelect (`TomSelectWidget`)
Keys
- `label`, `help`
- `multiple`: boolean. When true, `name` becomes `name[]` and `multiple` attribute is set
- When NOT using AJAX
  - `choices`: array or callable returning `[value => label]`
- When using AJAX
  - `tomselect`: array with
    - `route`: URL used by Select2 AJAX
    - `model`: FQCN used to preload current value(s) into choices
    - `label_field`: defaults to `name`
    - `value_field`: defaults to `id`
    - `placeholder`: string for Select2 placeholder (default: "Select")
    - `dropdownParent`: CSS selector for dropdown parent (default: `body`)
    - `theme`: theme string (default: `bootstrap-5` in the Bootstrap template)
    - Dependencies (optional)
      - `depends_on`: parent field id. Child select is disabled until parent has value
      - `depends_param`: request param name sent to AJAX; defaults to `depends_on` if omitted
      - Behavior: on parent change, child is cleared; AJAX requests include `{ term, [depends_param]: parentValue }`
  - Defaults in template when AJAX is enabled: `minimumInputLength=2`, `delay=250ms`
- `attributes`: standard HTML attributes (e.g., `id`, `class`). Default `class` includes `tomselect`.

## Radio (`RadioWidget`)
Keys
- `label`, `help`
- `options`: array or callable returning `[value => label]`
- `attributes`: applied to each radio input. `class` is merged.

## Checkboxes (`CheckboxesWidget`)
Keys
- `label`, `help`
- `choices`: array or callable returning `[value => label]`
- `select-all`: array (optional)
  - `enabled`: boolean to show a Select All control
  - `label`: string for the control text (default: "Select all / Unselect all")
- `attributes`: applied to each checkbox input; `id` base used for grouping

## Toggle (`ToggleWidget`)
Keys
- `label`, `help`
- `attributes`: merged with defaults
  - Sets `type=checkbox` and `role=switch`
  - If the value is truthy, `checked` is set

## Range (`RangeWidget`)
Keys
- `label`, `help`
- `attributes`:
  - `type=range` (set automatically)
  - `min` (default 0)
  - `max` (default 100)
  - `step` (default 1)
- `showValue`: boolean (default true) for templates that display the current value

## Date (`DateWidget`)
Keys
- `label`, `help`
- `format`: PHP date format for incoming/outgoing value (default `Y-m-d`)
- `flatpickr`: array of Flatpickr options, merged with defaults
  - Defaults: `altInput=true`, `altFormat='d F Y'`, `dateFormat='Y-m-d'`, `locale='it'`
- `attributes`:
  - `type=text` (Flatpickr attaches to text inputs)
  - `data-formello-datepicker` set with merged options (JSON)
- `prefix`, `suffix`, `icon` supported by templates

## DateTime (`DateTimeWidget`)
Extends Date.
Keys
- Inherits all Date keys
- Different defaults merged into `flatpickr`:
  - `altFormat='d F Y H:i'`, `dateFormat='Y-m-d H:i'`, `enableTime=true`, `time_24hr=true`
- `format` default: `Y-m-d H:i`

## Upload (`UploadWidget`)
Keys
- `label`, `help`
- `type`: file input type (default `file`)
- `attributes`: `id`, `class`, and any file input attributes

## Color (`ColorWidget`)
Keys
- `label`, `help`
- `pickr`: array of Pickr options, merged with defaults
  - Defaults include: `theme='nano'`, `default` color (current value), components (preview, opacity, hue, interaction with hex, rgba, input, clear, save)
- `attributes`:
  - `type=text` and `data-formello-colorpicker` set with merged options (JSON)
- `prefix`, `suffix`, `icon` supported by templates

## ColorSwatch (`ColorSwatchWidget`)
Extends Color. Uses the same template as Color.
Keys
- Inherits all Color keys
- Different default Pickr options appropriate for swatches-only mode
  - Disables preview/opacity/hue and interaction controls
  - Provides a default `swatches` array you can override via `pickr.swatches`

## Wysiwyg (`WysiwygWidget`)
Keys
- `label`, `help`
- `jodit`: array of Jodit options; applied via `data-formello-wysiwyg` (JSON)
- `attributes`: textarea HTML attributes (e.g., rows)

---

## Assets and Framework Config
- See `config/formello.php`:
  - `css_framework`: `bootstrap5` or `tailwindcss4`
  - `default_widgets`: map widget names to classes
  - `assets`: enable/disable library loading per widget type (e.g., `select2`, `date`, `datetime`, `mask`, `color`, `colorswatch`, `wysiwyg`)

## Examples
Minimal field definitions in a Formello form class:
```php
protected function fields(): array
{
    return [
        'name' => [
            'label' => 'Name',
            'help' => 'Enter your full name',
            'attributes' => ['placeholder' => 'John Doe'],
        ],
        'category_id' => [
            'label' => 'Category',
            'widget' => 'select',
            'choices' => fn () => Category::pluck('name', 'id')->toArray(),
            'multiple' => true,
        ],
        'publish_at' => [
            'label' => 'Publish at',
            'widget' => 'date',
            'format' => 'Y-m-d',
            'flatpickr' => ['altFormat' => 'd/m/Y'],
        ],
        'content' => [
            'label' => 'Content',
            'widget' => 'wysiwyg',
            'jodit' => ['toolbarAdaptive' => false],
        ],
    ];
}
```
