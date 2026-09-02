# Formello Reactive System

The reactive system enables field interdependencies without writing custom JavaScript from scratch. It uses a callback-based approach: the field config declares **what to call**, and the logic lives in **callbacks** (JS or PHP).

## Core Concepts

- **Two execution layers**: `reactive.client` (JS, instant) and `reactive.server` (PHP, async round-trip)
- **Identical State API**: `get/set/setOptions/setAttributes` works the same in both JS and PHP
- **On-top of widgets**: reactive works via `data-*` attributes on field wrappers. Widgets don't need any reactive-specific code — custom widgets get reactivity for free

## Quick Start

### 1. Define reactive fields

Use the `->reactive()` method on `FormelloField`:

```php
protected function fields(): array
{
    return [
        FormelloField::make('total')
            ->label('Amount')
            ->widget('mask')
            ->reactive([
                'client' => 'calculateYearlyTotal',
            ]),

        FormelloField::make('contract_duration')
            ->label('Duration (months)')
            ->type('number')
            ->reactive([
                'client' => 'calculateYearlyTotal',
            ]),

        FormelloField::make('total_yearly')
            ->label('Yearly Amount')
            ->readonly(),
    ];
}
```

### 2. Write client-side callbacks

Register callbacks on `window.FormelloReactive`:

```js
window.FormelloReactive = {
    calculateYearlyTotal(state) {
        const total = parseFloat(state.get('total')) || 0;
        const duration = parseFloat(state.get('contract_duration')) || 0;
        if (duration > 0) {
            state.set('total_yearly', ((total / duration) * 12).toFixed(2));
        }
    },
};
```

### 3. Publish assets

```bash
php artisan vendor:publish --tag=formello-assets --force
```

## Field Config

The `reactive` key accepts `client`, `server`, or both:

```php
// Client only (instant JS)
->reactive(['client' => 'myCallback'])

// Server only (async PHP)
->reactive(['server' => 'onFieldChanged'])

// Both layers: client runs first, then server
->reactive([
    'client' => 'quickEstimate',
    'server' => 'preciseCalculation',
])

// Multiple callbacks on the same trigger
->reactive([
    'client' => ['updateSubtotal', 'updateShipping'],
])
```

## State API

The same API is available in both JS (`FormelloClientState`) and PHP (`FormelloState`):

| Method | Description |
|--------|-------------|
| `get(field)` | Read current value of a field |
| `set(field, value)` | Set value of a field |
| `setOptions(field, {key: label})` | Replace options of a select/tomselect |
| `setAttributes(field, {attrs})` | Set UI attributes on a field |

### Supported attribute keys

| Key | Type | Effect |
|-----|------|--------|
| `disabled` | bool | Disable/enable the field input |
| `readonly` | bool | Make field read-only |
| `required` | bool | Toggle required attribute |
| `hidden` | bool | Hide/show the entire field wrapper (label + input + help) |
| `placeholder` | string | Change placeholder text |
| `class` | string/array | Add CSS class(es) to the input |
| `removeClass` | string/array | Remove CSS class(es) from the input |

## Client-Side Examples

### Show/hide a field conditionally

```js
window.FormelloReactive = {
    onStatusChanged(state) {
        const is_other = state.get('status') === 'other';
        state.setAttributes('status_other', {
            hidden: !is_other,
            required: is_other,
        });
        if (!is_other) state.set('status_other', '');
    },
};
```

### Geocoding with external API

```js
window.FormelloReactive = {
    geocodeAddress(state) {
        const address = [
            state.get('street'),
            state.get('city'),
            state.get('zip'),
        ].filter(Boolean).join(', ');

        if (address.length < 5) return;

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`)
            .then(r => r.json())
            .then(data => {
                if (data.length) {
                    state.set('latitude', data[0].lat);
                    state.set('longitude', data[0].lon);
                }
            });
    },
};
```

## Server-Side Callbacks (PHP)

Server callbacks are methods on your form class. They receive a `FormelloState` instance:

```php
use Metalogico\Formello\Support\FormelloState;

class ContractForm extends Formello
{
    public function onRegionChanged(FormelloState $state): void
    {
        $region_id = $state->get('region_id');
        $provinces = Province::where('region_id', $region_id)
            ->pluck('name', 'id')->toArray();

        $state->setOptions('province_id', $provinces);
        $state->set('province_id', null);
    }
}
```

Field config:

```php
FormelloField::make('region_id')
    ->widget('tomselect')
    ->reactive(['server' => 'onRegionChanged']),

FormelloField::make('province_id')
    ->widget('tomselect'),
```

## Configuration

In `config/formello.php`:

```php
'reactive' => [
    'compute_path' => '/formello/compute',
    'middleware' => ['web', 'auth'],
    'allowed_forms' => [
        App\Forms\ContractForm::class,
    ],
    // Optional: deny model load (IDOR protection)
    // 'authorize_model' => fn ($request, $model) => $request->user()?->can('view', $model) ?? false,
],
```

- **`compute_path`**: The POST endpoint for server callbacks. Set to `null`/`false` to disable the route.
- **`middleware`**: Applied to the compute route. Defaults to `['web', 'auth']` (session, CSRF, authentication). Use `['web']` only if guests must call server callbacks.
- **`allowed_forms`**: Whitelist of form FQCNs. **Empty = reject all (fail-closed).** Required in production for any server callback. Use `['*']` only for local development to allow every `Formello` subclass.
- **`authorize_model`**: Optional callable `fn (Request $request, $model): bool`. Return `false` to respond with 403 before the form is instantiated. Use this to enforce policies on `model_class` / `model_id` from the client.

### Production checklist (server callbacks)

1. Publish config and list every form that uses `reactive.server` in `allowed_forms`.
2. Keep `auth` (or your app equivalent) in `middleware`.
3. Set `authorize_model` when edit forms load models by id from the request.

## Execution Flow

1. User changes a field value
2. JS engine checks the `data-formello-reactive` map
3. If `reactive.client` is defined → callback runs instantly, changes applied to DOM
4. If `reactive.server` is defined → POST to `/formello/compute`, response applied to DOM
5. When both are defined, client runs first (instant feedback), then server (may override)

## Initial State

All client callbacks run once on page load (deduplicated). This ensures fields are in the correct initial state — e.g., conditionally hidden fields are hidden from the start.

## Notes

- **Single form per page** (for now)
- **No loop prevention** — the form author is responsible for avoiding circular triggers
- **CSRF**: The JS engine reads the token from `<meta name="csrf-token">` (standard Laravel setup)
- **Widget-aware**: `setFieldValue` in JS handles TomSelect, Flatpickr, and Jodit correctly
- The `depends_on` mechanism in TomSelectWidget still works for simple AJAX dependencies. The reactive system is for anything more complex
