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
        @if (isset($config['attributes']['data-formello-mask'])) data-formello-mask='{{ $config['attributes']['data-formello-mask'] }}' @endif
        @foreach ($config['attributes'] as $attr => $attrValue)
            @if (!in_array($attr, ['data-formello-mask']))
                {{ $attr }}="{{ $attrValue }}"
            @endif
        @endforeach>

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
