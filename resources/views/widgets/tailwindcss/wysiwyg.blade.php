<div class="mb-4">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] ?? '' }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif

    @php
        $errorState = $errors ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '';
    @endphp

    <textarea name="{{ $name }}"
        class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 {{ $errorState }} {{ $config['attributes']['class'] ?? '' }}"
        @foreach ($config['attributes'] as $attr => $attrValue)
            @if ($attr !== 'class') {{ $attr }}="{{ $attrValue }}" @endif
        @endforeach>{{ old($name, $value) }}</textarea>

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
