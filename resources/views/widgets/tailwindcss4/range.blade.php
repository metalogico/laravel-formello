<div class="mb-4">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if ($showValue)
                <span id="{{ $config['attributes']['id'] }}_value" class="ml-2 text-gray-500">{{ $value }}</span>
            @endif
        </label>
    @endif

    @php
        $errorState = $errors ? 'accent-red-500' : '';
    @endphp

    <input
        type="range"
        name="{{ $name }}"
        value="{{ $value }}"
        class="mt-2 w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider {{ $errorState }} {{ $config['attributes']['class'] ?? '' }}"
        @foreach ($config['attributes'] as $attr => $attrValue)
            @if(!in_array($attr, ['class','type','value','name'])) {{ $attr }}="{{ $attrValue }}" @endif
        @endforeach
    >

    @if (isset($config['help']))
        <p class="mt-2 text-sm text-gray-500">{!! $config['help'] !!}</p>
    @endif

    @if ($errors)
        <ul class="mt-2 text-sm text-red-600 space-y-1">
            @foreach ($errors as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
</div>

@if ($showValue)
<script>
    document.getElementById('{{ $config['attributes']['id'] }}').addEventListener('input', function(e) {
        var el = document.getElementById('{{ $config['attributes']['id'] }}_value');
        if (el) el.textContent = e.target.value;
    });
    </script>
@endif

@once
<style>
    .slider::-webkit-slider-thumb {
        appearance: none;
        height: 20px;
        width: 20px;
        border-radius: 50%;
        background: #3B82F6;
        cursor: pointer;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    .slider::-moz-range-thumb {
        height: 20px;
        width: 20px;
        border-radius: 50%;
        background: #3B82F6;
        cursor: pointer;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
</style>
@endonce