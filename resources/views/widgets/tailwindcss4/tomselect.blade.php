<div class="mb-4">

    @if (isset($label))
        <label for="{{ $config['attributes']['id'] }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif

    @php
        $errorState = $errors ? 'border-red-500 focus:border-red-500 focus:ring-red-300' : '';
    @endphp

    <select name="{{ $name }}"
        class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-300 {{ $errorState }} {{ $config['attributes']['class'] ?? '' }}"
        @foreach ($config['attributes'] as $attr => $attrValue) @if($attr !== 'class') {{ $attr }}="{{ $attrValue }}" @endif @endforeach>
        @if (isset($config['placeholder']))
            <option value="">{{ $config['placeholder'] }}</option>
        @endif
        @foreach ($choices as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}"
                {{ in_array($optionValue, (array)old($name, $value)) ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

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

    @once
        @push('formello-scripts')
            <script>
            (function(){
              function initAllTomSelects(){
                var nodes = document.querySelectorAll('select[data-formello-tomselect]');
                if (typeof TomSelect === 'undefined') { setTimeout(initAllTomSelects, 50); return; }

                nodes.forEach(function(el){
                  if (el.dataset.tsInitialized === '1') return;
                  el.dataset.tsInitialized = '1';

                  try {
                    var options = {};
                    try { options = JSON.parse(el.getAttribute('data-formello-tomselect') || '{}'); } catch(e){}

                    var valueField = options.valueField || 'id';
                    var labelField = options.labelField || 'text';
                    var searchField = options.searchField || 'text';
                    var placeholder = options.placeholder || '';

                    var tsOptions = {
                      valueField: valueField,
                      labelField: labelField,
                      searchField: searchField,
                      placeholder: placeholder,
                      create: false,
                    };

                    if (options.ajax && options.ajax.url) {
                      var ajax = options.ajax;
                      var minLength = parseInt(ajax.minLength || 0, 10);
                      var dependsOnId = ajax.depends_on || null;
                      var dependsParam = ajax.depends_param || dependsOnId;
                      var parentEl = dependsOnId ? document.getElementById(dependsOnId) : null;

                      if (parentEl) {
                        var pv = parentEl.value;
                        if (!pv || (Array.isArray(pv) && pv.length === 0)) {
                          el.setAttribute('disabled', 'disabled');
                        }
                        parentEl.addEventListener('change', function(){
                          var pv = parentEl.value;
                          var disable = !pv || (Array.isArray(pv) && pv.length === 0);
                          if (disable) {
                            el.setAttribute('disabled','disabled');
                            el.value = '';
                            el.dispatchEvent(new Event('change', { bubbles: true }));
                          } else {
                            el.removeAttribute('disabled');
                          }
                        });
                      }

                      tsOptions.load = function(query, callback){
                        try {
                          if (minLength && (!query || query.length < minLength)) return callback();
                          var params = new URLSearchParams();
                          if (query) params.set('term', query);
                          if (parentEl && dependsParam) {
                            var pv = parentEl.value;
                            if (!pv || (Array.isArray(pv) && pv.length === 0)) return callback();
                            params.set(dependsParam, pv);
                          }
                          var url = ajax.url + (ajax.url.indexOf('?') !== -1 ? '&' : '?') + params.toString();
                          fetch(url, { headers: { 'Accept': 'application/json' } })
                            .then(function(r){ return r.json(); })
                            .then(function(data){ callback((data && data.results) || []); })
                            .catch(function(){ callback(); });
                        } catch(e) {
                          console.error('Formello Tom Select load error:', e);
                          callback();
                        }
                      };
                    }

                    new TomSelect(el, tsOptions);
                  } catch (e) {
                    console.error('Formello Tom Select init error:', e);
                  }
                });
              }

              if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initAllTomSelects);
              } else {
                initAllTomSelects();
              }
            })();
            </script>
        @endpush
    @endonce
</div>
