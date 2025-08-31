<div class="mb-4">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif

    @php
        $hasGroup = isset($config['icon']) || isset($config['prefix']) || isset($config['suffix']);
        $errorState = $errors ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '';
    @endphp

    @if ($hasGroup)
        <div class="mt-1 flex rounded-md">
            @if (isset($config['prefix']) || isset($config['icon']))
                <span class="inline-flex items-center rounded-l-md border border-gray-300 bg-gray-50 px-3 text-sm text-gray-500 gap-1">
                    @if (isset($config['icon'])) <i class="{!! $config['icon'] !!}"></i> @endif
                    @if (isset($config['prefix'])) {{ $config['prefix'] }} @endif
                </span>
            @endif

            <input name="{{ $name }}" value="{{ old($name, $value) }}"
                class="block w-full min-w-0 flex-1 rounded-none {{ (isset($config['prefix']) || isset($config['icon'])) ? 'rounded-r-md' : 'rounded-md' }} border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 {{ $errorState }} {{ $config['attributes']['class'] ?? '' }}"
                @foreach ($config['attributes'] as $attr => $attrValue)
                    @if ($attr !== 'class') {{ $attr }}="{{ $attrValue }}" @endif
                @endforeach>

            @if (isset($config['suffix']))
                <span class="inline-flex items-center rounded-r-md border border-gray-300 bg-gray-50 px-3 text-sm text-gray-500">{{ $config['suffix'] }}</span>
            @endif
        </div>
    @else
        <input name="{{ $name }}" value="{{ old($name, $value) }}"
            class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 {{ $errorState }} {{ $config['attributes']['class'] ?? '' }}"
            @foreach ($config['attributes'] as $attr => $attrValue)
                @if ($attr !== 'class') {{ $attr }}="{{ $attrValue }}" @endif
            @endforeach>
    @endif

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
