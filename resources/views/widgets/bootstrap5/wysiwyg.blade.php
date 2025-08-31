<div class="form-group mb-3">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="form-label">{{ $label }}</label>
    @endif

    <textarea data-formello-wysiwyg="{{ json_encode($fieldConfig['jodit'] ?? []) }}" name="{{ $name }}" class="form-control {{ $config['attributes']['class'] ?? '' }} @if ($errors) is-invalid @endif"
        @foreach ($config['attributes'] as $attr => $attrValue) @if($attr !== 'class') {{ $attr }}="{{ $attrValue }}" @endif @endforeach>{{ old($name, $value) }}</textarea>

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
