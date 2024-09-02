<div class="form-group mb-3">
    
    @if (isset($label))
        <label class="form-label">{{ $label }}</label>
    @endif
    
    @foreach ($options as $optionValue => $optionLabel)
        <div class="form-check">
            <input type="radio" 
                   name="{{ $name }}" 
                   id="{{ $name }}_{{ $optionValue }}" 
                   value="{{ $optionValue }}"
                   {{ $value == $optionValue ? 'checked' : '' }}
                   class="form-check-input @if ($errors) is-invalid @endif"
                   @foreach ($config['attributes'] as $attr => $attrValue) 
                       {{ $attr }}="{{ $attrValue }}"
                   @endforeach
            >
            <label class="form-check-label" for="{{ $name }}_{{ $optionValue }}">
                {{ $optionLabel }}
            </label>
        </div>
    @endforeach

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