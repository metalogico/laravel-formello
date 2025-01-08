<div class="form-group">
    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="form-label">{{ $label }}</label>
    @endif

    @foreach($choices as $optionValue => $optionLabel)
        <div class="form-check mb-3">
            <input
                @foreach ($config['attributes'] as $attr => $attrValue) {{ $attr }}="{{ $attrValue }}" @endforeach
                type="checkbox"
                name="{{ $name }}[]"
                value="{{ $optionValue }}"
                class="{{ $attributes['class'] ?? '' }} @if ($errors) is-invalid @endif"
                id="{{ $name }}_{{ $optionValue }}"
                {{ in_array($optionValue, (array)old($name, $value)) ? 'checked' : '' }}
            >
            <label class="form-check-label" for="{{ $name }}_{{ $optionValue }}">
                {{ $optionLabel }}
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
