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
                data-formello-select2="true"
                class="form-select rounded-start-0 {{ $config['attributes']['class'] ?? '' }}"
                @if($usesAjax)
                    data-ajax--url="{{ $config['select2']['route'] }}"
                    data-ajax--cache="true"
                    data-ajax--delay="250"
                    data-minimum-input-length="2"
                @endif
                data-placeholder="{{ $config['select2']['placeholder'] ?? __('Select') }}"
                data-allow-clear="true"
                data-dropdown-parent="{{ $config['select2']['dropdownParent'] ?? 'body' }}"
                data-theme="{{ $config['select2']['theme'] ?? 'bootstrap-5' }}"
                @if(!empty(data_get($config, 'select2.depends_on')) && (empty($value) || (is_array($value) && count($value) === 0)))
                    disabled
                @endif
                @if($config['multiple']) multiple="multiple" @endif
                @foreach ($config['attributes'] as $attr => $attrValue)
                    @if (!in_array($attr, ['id','class','multiple']))
                        {{ $attr }}="{{ $attrValue }}"
                    @endif
                @endforeach
                >
                @if(!$config['multiple']) <option></option> @endif
                @foreach ($choices as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" {{ in_array($optionValue, (array)$value) ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    @if (!empty(data_get($config, 'select2.depends_on')))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var childId = @json($config['attributes']['id']);
                var parentId = @json(data_get($config, 'select2.depends_on'));
                // Compute dependsParam without using @php blocks
                var dependsParam = @json(data_get($config, 'select2.depends_param')) || @json(data_get($config, 'select2.depends_on'));

                var childEl = document.getElementById(childId);
                var parentEl = document.getElementById(parentId);
                if (!childEl) return;

                if (typeof $ === 'undefined' || !$.fn.select2) {
                    console.warn('Select2 not loaded but dependent Select2 found. Include jQuery and Select2 before this template.');
                    return;
                }

                var ajaxUrl = childEl.getAttribute('data-ajax--url');
                var ajaxDelay = parseInt(childEl.getAttribute('data-ajax--delay') || '250', 10);
                var minLenAttr = childEl.getAttribute('data-minimum-input-length');
                var minLen = minLenAttr ? parseInt(minLenAttr, 10) : 0;
                var dropdownParentSel = childEl.getAttribute('data-dropdown-parent') || 'body';
                var theme = childEl.getAttribute('data-theme') || 'bootstrap-5';

                function getParentValue() {
                    if (!parentEl) return '';
                    return $(parentEl).val();
                }

                // If parent empty and child has no value, keep disabled before init
                var childHasValue = !!($(childEl).val() && $(childEl).val().length);
                var pval = getParentValue();
                if ((!pval || (Array.isArray(pval) && pval.length === 0)) && !childHasValue) {
                    $(childEl).prop('disabled', true);
                }

                var options = {
                    theme: theme,
                    minimumInputLength: minLen,
                    dropdownParent: $(dropdownParentSel),
                    allowClear: true
                };

                if (ajaxUrl) {
                    options.ajax = {
                        url: ajaxUrl,
                        delay: ajaxDelay,
                        data: function (params) {
                            var data = { term: params.term };
                            data[dependsParam] = getParentValue();
                            return data;
                        },
                        processResults: function (data) { return data; }
                    };
                }

                $(childEl).select2(options);

                if (parentEl) {
                    $(parentEl).on('change', function () {
                        var current = getParentValue();
                        if (!current || (Array.isArray(current) && current.length === 0)) {
                            $(childEl).val(null).trigger('change');
                            $(childEl).prop('disabled', true);
                        } else {
                            $(childEl).prop('disabled', false);
                            // Clear previous selection when parent changes to prevent mismatches
                            $(childEl).val(null).trigger('change');
                        }
                    });
                }
            });
        </script>
    @endif

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

