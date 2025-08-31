<div class="mb-4">

    @if (isset($label))
        <span class="block text-sm font-medium text-gray-700">{{ $label }}</span>
    @endif

    @php
        $colorState = $errors ? 'text-red-600 focus:ring-red-300' : 'text-blue-600 focus:ring-blue-300';
    @endphp

    <div class="mt-2 space-y-2">
        @foreach ($options as $optionValue => $optionLabel)
            @php $optionId = $name . '_' . $optionValue; @endphp
            <label for="{{ $optionId }}" class="flex items-center gap-2 text-sm text-gray-700">
                <input type="radio"
                       name="{{ $name }}"
                       id="{{ $optionId }}"
                       value="{{ $optionValue }}"
                       {{ $value == $optionValue ? 'checked' : '' }}
                       class="h-4 w-4 bg-white border-gray-300 focus:ring-1 {{ $colorState }} {{ $config['attributes']['class'] ?? '' }}"
                       @foreach ($config['attributes'] as $attr => $attrValue)
                           @if ($attr !== 'class' && $attr !== 'id') {{ $attr }}="{{ $attrValue }}" @endif
                       @endforeach
                >
                <span>{{ $optionLabel }}</span>
            </label>
        @endforeach
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
