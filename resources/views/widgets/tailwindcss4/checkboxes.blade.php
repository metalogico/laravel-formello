<div class="mb-4">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif

    @if(isset($config['select-all']['enabled']))
        <div class="mt-2 mb-2">
            <label class="flex items-center space-x-3 cursor-pointer">
                <input type="checkbox" id="select-all-{{ $config['attributes']['id'] }}" class="h-4 w-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-300 focus:ring-1">
                <span class="text-gray-900">{{ $config['select-all']['label'] ?? 'Select all / Unselect all' }}</span>
            </label>
        </div>
    @endif

    <div class="space-y-2">
        @foreach($choices as $optionValue => $optionLabel)
            <label for="{{ $name }}_{{ $optionValue }}" class="flex items-center space-x-3 cursor-pointer">
                <input
                    type="checkbox"
                    name="{{ $name }}[]"
                    value="{{ $optionValue }}"
                    id="{{ $name }}_{{ $optionValue }}"
                    class="{{ $name }}-checkbox h-4 w-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-200 focus:ring-1 {{ $errors ? 'border-red-500 focus:ring-red-300' : '' }} {{ $config['attributes']['class'] ?? '' }}"
                    {{ in_array($optionValue, (array) $value) ? 'checked' : '' }}
                    @foreach ($config['attributes'] as $attr => $attrValue)
                        @if ($attr !== 'class' && $attr !== 'id') {{ $attr }}="{{ $attrValue }}" @endif
                    @endforeach
                >
                <span class="text-gray-900">{{ $optionLabel }}</span>
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

    @if(isset($config['select-all']['enabled']))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const selectAll = document.getElementById('select-all-{{ $config['attributes']['id'] }}');
                if (!selectAll) return;
                const boxes = document.querySelectorAll('.{{ $name }}-checkbox');

                selectAll.addEventListener('change', function () {
                    const isChecked = selectAll.checked;
                    boxes.forEach(cb => cb.checked = isChecked);
                });

                boxes.forEach(cb => {
                    cb.addEventListener('change', function () {
                        const allChecked = Array.from(boxes).every(b => b.checked);
                        const anyChecked = Array.from(boxes).some(b => b.checked);
                        selectAll.checked = allChecked;
                        selectAll.indeterminate = !allChecked && anyChecked;
                    });
                });
            });
        </script>
    @endif

</div>
