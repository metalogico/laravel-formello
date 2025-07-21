<div class="form-group mb-3">
    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="form-label">{{ $label }}</label>
    @endif

    <div class="input-group flex-nowrap">
        <span class="input-group-text">
            <i class="fas fa-search fs-6"></i>
        </span>
        <div class="overflow-hidden flex-grow-1">
            <select id="{{ $config['attributes']['id'] }}"
                name="{{ $name }}"
                class="form-select rounded-start-0"
                data-control="select2"
                @if($config['multiple'])
                    multiple
                @endif
                @if($usesAjax)
                    data-ajax--url="{{ $config['select2']['route'] }}"
                    data-ajax--cache="true"
                    data-ajax--delay="250"
                    data-minimum-input-length="2"
                @endif
                data-placeholder="{{ $config['placeholder'] ?? __('Select') }}"
                data-allow-clear="true"
                data-language="it"
                data-dropdown-parent="{{ $config['dropdownParent'] ?? 'body' }}">

                {{-- Render pre-selected options for AJAX or all options for non-AJAX --}}
                @foreach ($choices as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" {{ in_array($optionValue, (array)$value) ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    @if (isset($config['help']))
        <div class="form-text">{!! $config['help'] !!}</div>
    @endif

    @if ($errors)
        <div class="invalid-feedback d-block">
            <ul class="mb-0">
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
