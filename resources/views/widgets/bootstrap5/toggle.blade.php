<div class="form-group mb-3">
    @if (isset($label))
        <label class="form-label" for="{{ $config['attributes']['id'] }}">{{ $label }}</label>
    @endif
    <div class="form-check form-switch">
        <input type="hidden" name="{{ $name }}" value="0">
        <input type="checkbox" name="{{ $name }}" value="1"
            class="form-check-input {{ $config['attributes']['class'] ?? '' }} @if ($errors) is-invalid @endif"
            @if ($checked) checked @endif
            @foreach ($config['attributes'] as $attr => $attrValue)
                @if (!in_array($attr, ['class', 'type', 'value', 'name', 'checked'])) {{ $attr }}="{{ $attrValue }}" @endif
            @endforeach>
    </div>
    @if (isset($config['help']))
        <div class="form-text">{!! $config['help'] !!}</div>
    @endif

    @if ($errors)
        <div class="invalid-feedback d-block">
            <ul>
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

</div>
