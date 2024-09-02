<div class="form-group mb-3">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="form-label">{{ $label }}</label>
    @endif
    
    <select name="{{ $name }}" class="form-select @if ($errors) is-invalid @endif"
        @foreach ($config['attributes'] as $attr => $attrValue) {{ $attr }}="{{ $attrValue }}" @endforeach>
        @if (isset($config['placeholder']))
            <option value="">{{ $config['placeholder'] }}</option>
        @endif
        @foreach ($choices as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" 
                {{ in_array($optionValue, (array)old($name, $value)) ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
    
    @if (isset($config['help']))
        <div class="form-text">{!! $config['help'] !!}</div>
    @endif

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