<div class="form-group mb-3">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="form-label">{{ $label }}</label>
    @endif

    @if (isset($config['icon']) || isset($config['prefix']) || isset($config['suffix']))
        <div class="input-group has-validation">
            @if (isset($config['prefix']))
                <span class="input-group-text">@if (isset($config['icon'])) <i class="{!! $config['icon'] !!}"></i> @endif {{ $config['prefix'] }}</span>
            @elseif (isset($config['icon']))
                <span class="input-group-text"><i class="{!! $config['icon'] !!}"></i></span>
            @endif
            <input name="{{ $name }}" value="{{ old($name, $value) }}"
                class="{{ $config['attributes']['class'] }} @if ($errors) is-invalid @endif"
                @foreach ($config['attributes'] as $attr => $attrValue)
                    @if ($attr !== 'class')
                        {{ $attr }}="{{ $attrValue }}"
                    @endif
                @endforeach>
            @if (isset($config['suffix']))
                <span class="input-group-text">{{ $config['suffix'] }}</span>
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
    @else
        <input name="{{ $name }}" value="{{ old($name, $value) }}"
            class="{{ $config['attributes']['class'] }} @if ($errors) is-invalid @endif"
            @foreach ($config['attributes'] as $attr => $attrValue)
                @if ($attr !== 'class')
                    {{ $attr }}="{{ $attrValue }}"
                @endif
            @endforeach>
        @if ($errors)
            <div class="invalid-feedback">
                <ul>
                    @foreach ($errors as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

    @if (isset($config['help']))
        <div class="form-text">{!! $config['help'] !!}</div>
    @endif

</div>
