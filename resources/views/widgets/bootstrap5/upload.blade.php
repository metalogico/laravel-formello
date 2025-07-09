<div class="form-group mb-3"> 
    
    <div class="custom-file">
        <input type="{{ $config['attributes']['type'] }}" name="{{ $name }}" value="{{ $value }}" 
            class="{{ $config['attributes']['class'] }} @if ($errors) is-invalid @endif"
            @foreach ($config['attributes'] as $attr => $attrValue) {{ $attr }}="{{ $attrValue }}" @endforeach>
        
        @if (isset($config['help']))
            <div class="form-text">{!! $config['help'] !!}</div>
        @endif

        @if (isset($label))
            <label for="{{ $config['attributes']['id'] }}" class="custom-file-label">{{ $label }}</label>
        @endif
    </div>

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
