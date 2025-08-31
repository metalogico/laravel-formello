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
          var ts = null;

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
                if (ts) { try { ts.disable(); } catch(e){} }
              } else {
                el.removeAttribute('disabled');
                // Clear and refresh child options so it fetches for the new parent
                if (ts) {
                  try {
                    ts.enable();
                    ts.clear();
                    ts.clearOptions();
                    // This triggers shouldLoad('') check and load if allowed
                    ts.refreshOptions(false);
                  } catch (e) {}
                }
              }
            });
          }

          // Only load when query satisfies minLength (0 means allow empty)
          tsOptions.shouldLoad = function(query){
            if (minLength && (!query || query.length < minLength)) return false;
            return true;
          };

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

        ts = new TomSelect(el, tsOptions);
        // Sync TS enabled state with current dependency value
        try {
          if (parentEl) {
            var pv = parentEl.value;
            var disable = !pv || (Array.isArray(pv) && pv.length === 0);
            if (disable) ts.disable(); else ts.enable();
          }
        } catch(e){}
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
