<div class="form-group">
    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="form-label">{{ $label }}</label>
    @endif

    @foreach($choices as $option)
        <div class="form-check mb-3">
            <input
                @foreach ($config['attributes'] as $attr => $attrValue) {{ $attr }}="{{ $attrValue }}" @endforeach
                type="checkbox"
                name="{{ $name }}[]"
                value="{{ $option['value'] }}"
                class="{{ $attributes['class'] ?? '' }} @if ($errors) is-invalid @endif"
                id="{{ $name }}_{{ $option['value'] }}"
                {{ in_array($option['value'], (array)old($name, $value)) ? 'checked' : '' }}
            >
            <label class="form-check-label" for="{{ $name }}_{{ $option['value'] }}">
                {{ $option['label'] }}
            </label>
        </div>
    @endforeach

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
