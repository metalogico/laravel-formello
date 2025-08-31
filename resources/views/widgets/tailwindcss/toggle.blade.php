<div class="mb-4">

    @if (isset($config['label']))
        <label for="{{ $config['attributes']['id'] }}" class="block text-sm font-medium text-gray-700">{{ $config['label'] }}</label>
    @endif

    @php
        $errorState = $errors ? 'ring-1 ring-red-300' : '';
    @endphp

    <div class="mt-2">
        <input type="hidden" name="{{ $name }}" value="0">

        <label class="inline-flex items-center cursor-pointer select-none">
            <input type="checkbox"
                name="{{ $name }}"
                value="1"
                class="sr-only peer"
                {{ old($name, $value) ? 'checked' : '' }}
                @foreach ($config['attributes'] as $attr => $attrValue)
                    @if (!in_array($attr, ['class','type','value','name'])) {{ $attr }}="{{ $attrValue }}" @endif
                @endforeach
            >

            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer-focus:outline-none peer-focus:ring-1 peer-focus:ring-green-300 after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border after:border-gray-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full {{ $errorState }}"></div>

            @if (!empty($config['toggle_text']))
                <span class="ms-3 text-gray-900">{{ $config['toggle_text'] }}</span>
            @endif
        </label>
    </div>

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
