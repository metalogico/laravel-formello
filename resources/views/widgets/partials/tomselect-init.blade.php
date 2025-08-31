@php
    $elementId = $elementId ?? ($config['attributes']['id'] ?? null);
@endphp
@if ($elementId)
<script>
(function(){
  var init = function(){
    var el = document.getElementById(@json($elementId));
    if (!el) { return; }
    if (typeof TomSelect === 'undefined') { return setTimeout(init, 50); }
    if (el.dataset.tsInitialized === '1') { return; }
    el.dataset.tsInitialized = '1';
    try {
      var options = {};
      try { options = JSON.parse(el.getAttribute('data-formello-tomselect') || '{}'); } catch (e) {}

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
        }

        tsOptions.load = function(query, callback) {
          try {
            if (minLength && (!query || query.length < minLength)) { return callback(); }
            var params = new URLSearchParams();
            if (query) params.set('term', query);
            if (parentEl && dependsParam) {
              var pv = parentEl.value;
              if (!pv || (Array.isArray(pv) && pv.length === 0)) { return callback(); }
              params.set(dependsParam, pv);
            }
            var url = ajax.url + (ajax.url.indexOf('?') !== -1 ? '&' : '?') + params.toString();
            fetch(url, { headers: { 'Accept': 'application/json' } })
              .then(function(r){ return r.json(); })
              .then(function(data){ callback((data && data.results) || []); })
              .catch(function(){ callback(); });
          } catch (e) {
            console.error('Formello Tom Select load error:', e);
            callback();
          }
        };

        if (parentEl) {
          parentEl.addEventListener('change', function(){
            var pv = parentEl.value;
            var shouldDisable = !pv || (Array.isArray(pv) && pv.length === 0);
            if (shouldDisable) {
              el.setAttribute('disabled', 'disabled');
              el.value = '';
              var event = new Event('change', { bubbles: true });
              el.dispatchEvent(event);
            } else {
              el.removeAttribute('disabled');
            }
          });
        }
      }

      new TomSelect(el, tsOptions);
    } catch (e) {
      console.error('Formello Tom Select init error:', e);
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
</script>
@endif
