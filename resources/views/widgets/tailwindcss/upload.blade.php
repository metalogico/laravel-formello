<div class="mb-4">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif

    @php
        // Error styles applied directly to the input
        $errorState = $errors ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '';
    @endphp

    <input type="file" name="{{ $name }}"
        class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none
               file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
               {{ $errorState }} {{ $config['attributes']['class'] ?? '' }}"
        @foreach ($config['attributes'] as $attr => $attrValue)
            @if ($attr !== 'class') {{ $attr }}="{{ $attrValue }}" @endif
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
