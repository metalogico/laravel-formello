<div class="form-group mb-3">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="form-label">{{ $label }}</label>
    @endif

    <textarea name="{{ $name }}" class="form-control @if ($errors) is-invalid @endif"
        @foreach ($config['attributes'] as $attr => $attrValue) {{ $attr }}="{{ $attrValue }}" @endforeach>{{ old($name, $value) }}</textarea>

    @if (isset($config['help']))        
        <div class="form-text">{{ $config['help'] }}</div>
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
