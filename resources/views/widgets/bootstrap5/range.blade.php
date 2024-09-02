<div class="form-group mb-3">
    
    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="form-label">
            {{ $label }}
            @if ($showValue)
                <span id="{{ $config['attributes']['id'] }}_value">{{ $value }}</span>
            @endif
        </label>
    @endif
    
    <input
        name="{{ $name }}"
        value="{{ $value }}"
        @foreach ($config['attributes'] as $attr => $attrValue)
            {{ $attr }}="{{ $attrValue }}"
        @endforeach
        @if ($errors) class="is-invalid" @endif
    >

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

@if ($showValue)
<script>
    document.getElementById('{{ $config['attributes']['id'] }}').addEventListener('input', function(e) {
        document.getElementById('{{ $config['attributes']['id'] }}_value').textContent = e.target.value;
    });
</script>
@endif