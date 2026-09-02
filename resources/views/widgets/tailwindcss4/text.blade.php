<div class="mb-4">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif

    @if (isset($config['prefix']) || isset($config['suffix']))
        <div class="mt-1 flex rounded-md shadow-sm">
            @if (isset($config['prefix']) || isset($config['icon']))
                <span class="inline-flex items-center rounded-l-md border border-gray-300 bg-gray-50 px-3 text-sm text-gray-500 gap-1">
                    @if (isset($config['icon'])) <i class="{!! $config['icon'] !!}"></i> @endif
                    @if (isset($config['prefix'])) {{ $config['prefix'] }} @endif
                </span>
            @endif

            <input name="{{ $name }}" value="{{ $value }}"
                class="block w-full min-w-0 flex-1 rounded-none {{ (isset($config['prefix']) || isset($config['icon'])) ? 'rounded-r-md' : 'rounded-md' }} bg-white text-gray-900 placeholder:text-gray-400 border border-gray-300 px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-300 focus:border-blue-300 transition {{ $errors ? 'border-red-500 focus:border-red-500 focus:ring-red-300' : '' }} {{ $config['attributes']['class'] ?? '' }}"
                @foreach ($config['attributes'] as $attr => $attrValue)
                    @if ($attr !== 'class') {{ $attr }}="{{ $attrValue }}" @endif
                @endforeach>

            @if (isset($config['suffix']))
                <span class="inline-flex items-center rounded-r-md border border-gray-300 bg-gray-50 px-3 text-sm text-gray-500">{{ $config['suffix'] }}</span>
            @endif
        </div>
    @elseif (isset($config['icon']))
        <div class="relative mt-1">
            <input name="{{ $name }}" value="{{ $value }}"
                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-300 focus:border-blue-500 transition {{ $errors ? 'border-red-500 focus:border-red-500 focus:ring-red-300' : '' }} {{ $config['attributes']['class'] ?? '' }}"
                @foreach ($config['attributes'] as $attr => $attrValue)
                    @if ($attr !== 'class') {{ $attr }}="{{ $attrValue }}" @endif
                @endforeach>
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="{!! $config['icon'] !!} text-gray-400"></i>
            </div>
        </div>
    @else
        <input name="{{ $name }}" value="{{ $value }}"
            class="mt-1 block w-full rounded-md bg-white text-gray-900 placeholder:text-gray-400 border border-gray-300 px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-300 focus:border-blue-500 transition {{ $errors ? 'border-red-500 focus:border-red-500 focus:ring-red-300' : '' }} {{ $config['attributes']['class'] ?? '' }}"
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
