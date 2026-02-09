<div class="mb-4">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif

    @php
        $errorState = $errors ? 'border-red-500 focus:border-red-500 focus:ring-red-300' : '';
    @endphp

    <textarea name="{{ $name }}"
        class="mt-1 block w-full rounded-md bg-white text-gray-900 placeholder:text-gray-400 border border-gray-300 px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-300 focus:border-blue-500 transition resize-y {{ $errorState }} {{ $config['attributes']['class'] ?? '' }}"
        @foreach ($config['attributes'] as $attr => $attrValue) @if($attr !== 'class') {{ $attr }}="{{ $attrValue }}" @endif @endforeach>{{ old($name, $value) }}</textarea>

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
