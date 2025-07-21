<div class="form-group">
    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="form-label">{{ $label }}</label>
    @endif

    <!-- "Check All / Uncheck All" Header Checkbox -->
    @if(isset($config['select-all']['enabled']) )
    <div class="mb-3">
        <label>
            <input type="checkbox" class="form-check-input" id="select-all-{{ $config['attributes']['id'] }}">
            <span class="text-muted fs-sm">{{ $config['select-all']['label'] ?? 'Check all / Uncheck all' }}</span>
        </label>
    </div>
    @endif

    @foreach($choices as $optionValue => $optionLabel)
        <div class="form-check mb-3">
            <input
                @foreach ($config['attributes'] as $attr => $attrValue) {{ $attr }}="{{ $attrValue }}" @endforeach
                type="checkbox"
                name="{{ $name }}[]"
                value="{{ $optionValue }}"
                class="form-check-input {{ $name }}-checkbox {{ $config['attributes']['class'] ?? '' }} @if ($errors) is-invalid @endif"
                id="{{ $name }}_{{ $optionValue }}"
                {{ in_array($optionValue, (array)old($name, $value)) ? 'checked' : '' }}
            >
            <label class="form-label p-0 m-0" for="{{ $name }}_{{ $optionValue }}">
                {{ $optionLabel }}
            </label>
        </div>
    @endforeach

    @if (isset($config['help']))
        <div class="form-text">{!! $config['help'] !!}</div>
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


@if(isset($config['select-all']['enabled'])) 
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById("select-all-{{ $config['attributes']['id'] }}");
        const checkboxes = document.querySelectorAll(".{{ $name }}-checkbox");

        console.log(checkboxes);

        // Handle "select all/unselect all" toggle
        selectAllCheckbox.addEventListener('change', function () {
            const isChecked = selectAllCheckbox.checked;
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = isChecked;
            });
        });

        // Update "select all" checkbox based on individual checkboxes
        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = !allChecked && anyChecked;
            });
        });
    });
</script>
@endif