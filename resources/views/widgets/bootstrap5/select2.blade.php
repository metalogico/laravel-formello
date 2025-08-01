<div class="form-group mb-3">
    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="form-label">{{ $label }}</label>
    @endif

    <div class="input-group flex-nowrap">
        @if (isset($config['icon']) || isset($config['prefix']) || isset($config['suffix']))
            @if (isset($config['prefix']))
                <span class="input-group-text">@if (isset($config['icon'])) <i class="{!! $config['icon'] !!}"></i> @endif {{ $config['prefix'] }}</span>
            @elseif (isset($config['icon']))
                <span class="input-group-text"><i class="{!! $config['icon'] !!}"></i></span>
            @endif
        @endif
        <div class="overflow-hidden flex-grow-1">
            <select id="{{ $config['attributes']['id'] }}"
                name="{{ $name }}"
                class="form-select rounded-start-0"
                data-control="select2"
                data-multiple="{{ $config['multiple'] ?? 'false' }}"
                @if($usesAjax)
                    data-ajax--url="{{ $config['select2']['route'] }}"
                    data-ajax--cache="true"
                    data-ajax--delay="250"
                    data-minimum-input-length="2"
                @endif
                data-placeholder="{{ $config['select2']['placeholder'] ?? __('Select') }}"
                data-allow-clear="true"
                data-language="it"
                data-dropdown-parent="{{ $config['select2']['dropdownParent'] ?? 'body' }}"
                data-theme="{{ $config['select2']['theme'] ?? 'bootstrap-5' }}"
                @foreach ($config['attributes'] as $attr => $attrValue)
                    {{ $attr }}="{{ $attrValue }}"
                @endforeach
                >


                {{-- Render pre-selected options for AJAX or all options for non-AJAX --}}
                @foreach ($choices as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" {{ in_array($optionValue, (array)$value) ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>
        </div>
        @if (isset($config['suffix']))
            <span class="input-group-text">{{ $config['suffix'] }}</span>
        @endif
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

