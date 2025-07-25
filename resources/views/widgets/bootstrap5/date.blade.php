<div class="form-group mb-3">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="form-label">{{ $label }}</label>
    @endif

    @if (isset($config['icon']))
        <div class="input-group">
            <span class="input-group-text"><i class="{!! $config['icon'] !!}"></i></span>
            <input name="{{ $name }}" value="{{ old($name, $value) }}" @foreach ($config['attributes'] as $attr => $attrValue) {{ $attr }}="{{ $attrValue }}" @endforeach>
        </div>
    @else
        <input name="{{ $name }}" value="{{ old($name, $value) }}" @foreach ($config['attributes'] as $attr => $attrValue) {{ $attr }}="{{ $attrValue }}" @endforeach>
    @endif

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


