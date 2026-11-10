<div class="form-group mb-3">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="form-label">{{ $label }}</label>
    @endif

    <select name="{{ $name }}"
        class="{{ $config['attributes']['class'] ?? '' }} @if ($errors) is-invalid @endif"
        @foreach ($config['attributes'] as $attr => $attrValue) @if($attr !== 'class') {{ $attr }}="{{ $attrValue }}" @endif @endforeach>
        @if (isset($config['placeholder']))
            <option value="">{{ $config['placeholder'] }}</option>
        @endif
        @foreach ($choices as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}"
                {{ in_array($optionValue, (array) $value) ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @if (isset($config['help']))
        <div class="form-text">{!! $config['help'] !!}</div>
    @endif

    @if ($errors)
        <div class="invalid-feedback d-block">
            <ul class="mb-0">
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @once
        @include('formello::widgets.partials.tomselect-script')
    @endonce
</div>
