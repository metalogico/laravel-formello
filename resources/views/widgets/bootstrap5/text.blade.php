<div class="form-group mb-3">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="form-label">{{ $label }}</label>
    @endif

    @if (isset($config['icon']))
        <div class="input-group">
            <span class="input-group-text"><i class="{!! $config['icon'] !!}"></i></span>
    @endif

    <input type="{{ $config['attributes']['type'] }}" name="{{ $name }}" value="{{ old($name, $value) }}"
        class="{{ $config['attributes']['class'] }} @if ($errors) is-invalid @endif"
        @foreach ($config['attributes'] as $attr => $attrValue) {{ $attr }}="{{ $attrValue }}" @endforeach>

    @if (isset($config['icon']))
        </div>
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
