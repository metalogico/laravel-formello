<div class="form-group mb-3">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="form-label">{{ $label }}</label>
    @endif

    <input name="{{ $name }}" value="{{ old($name, $value) }}"
        class="form-control @if ($errors) is-invalid @endif"
        @foreach ($config['attributes'] as $attr => $attrValue)
            {{ $attr }}="{{ $attrValue }}" @endforeach>
    
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

@if ($format !== 'Y-m-d')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var input = document.getElementById('{{ $config['attributes']['id'] }}');
            input.addEventListener('change', function() {
                if (this.value) {
                    var date = new Date(this.value);
                    this.value = date.toLocaleDateString('en-CA', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit'
                    }).replace(/\//g, '-');
                }
            });
        });
    </script>
@endif
