<script>
(function(){
  // Resolve framework server-side (used to tailor default font size)
  var cssFramework = '{{ app('formello')->getCssFramework() }}';

  function ensureAsset(url, type, id){
    if (id && document.getElementById(id)) return Promise.resolve();
    if (type === 'css') {
      var exists = Array.from(document.styleSheets).some(function(s){ return (s.href||'').indexOf(url) !== -1; });
      if (exists) return Promise.resolve();
      return new Promise(function(resolve){
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = url;
        if (id) link.id = id;
        link.onload = function(){ resolve(); };
        document.head.appendChild(link);
      });
    }
    if (type === 'js') {
      var existsScript = Array.from(document.scripts).some(function(s){ return (s.src||'').indexOf(url) !== -1; });
      if (existsScript || (id && document.getElementById(id))) return Promise.resolve();
      return new Promise(function(resolve){
        var script = document.createElement('script');
        script.src = url;
        if (id) script.id = id;
        script.onload = function(){ resolve(); };
        document.body.appendChild(script);
      });
    }
    return Promise.resolve();
  }

  function waitFor(cond, timeoutMs){
    var start = Date.now();
    return new Promise(function(resolve, reject){
      (function tick(){
        if (cond()) return resolve();
        if (Date.now()-start > (timeoutMs||5000)) return reject(new Error('timeout'));
        setTimeout(tick, 50);
      })();
    });
  }

  function initAllJodit(){
    var nodes = document.querySelectorAll('[data-formello-wysiwyg]');
    if (!nodes.length) return;

    nodes.forEach(function(el){
          if (el.dataset.joditInitialized === '1') return;

          try {
            var options = {};
            try { options = JSON.parse(el.getAttribute('data-formello-wysiwyg') || '{}'); } catch(e){}

            var baseFont = (cssFramework === 'tailwindcss4') ? '0.875rem' : '1rem';
            // Compute typography from the host page to mirror it in the editor iframe
            var host = el.closest('.formello-field') || el.parentElement || document.body;
            var csHost = window.getComputedStyle(host);
            var csBody = window.getComputedStyle(document.body);
            var fontFamily = csHost.fontFamily || csBody.fontFamily || 'inherit';
            var fontSize = csHost.fontSize || baseFont;
            var lineHeight = (csHost.lineHeight && csHost.lineHeight !== 'normal') ? csHost.lineHeight : (csBody.lineHeight || '1.5');
            var color = csHost.color || csBody.color || 'inherit';

            var defaultConfig = {
              minHeight: 300,
              maxHeight: 300,
              iframe: true,
              // Ensure lists render properly inside the iframe document
              iframeStyle: 'html,body{font-family:'+fontFamily+';font-size:'+fontSize+';line-height:'+lineHeight+';color:'+color+';} ul{list-style:disc;margin:0 0 1em 1.25em;padding:0;} ol{list-style:decimal;margin:0 0 1em 1.25em;padding:0;} li{margin:0.25em 0;} p{margin:0 0 1em;}',
              toolbarSticky: false,
              safeMode: false,
              showCharsCounter: false,
              showWordsCounter: false,
              showXPathInStatusbar: false,
              buttons: [
                'bold','italic','underline','strikethrough','superscript','subscript',
                '|',
                'ul','ol',
                '|',
                'fontsize','eraser',
                '|',
                'link','unlink',
                '|',
                'left','center','right',
                '|',
                'undo','redo',
                '|',
                'hr'
              ],
              removeButtons: ['about','print']
            };

            var finalConfig = Object.assign({}, defaultConfig, options || {});
            var editor = new Jodit(el, finalConfig);
            el.dataset.joditInitialized = '1';
            editor.events.on('change', function(){
              el.dispatchEvent(new Event('change', { bubbles: true }));
            });
          } catch (e) {
            console.error('Formello Jodit init error:', e);
          }
        });

  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllJodit);
  } else {
    initAllJodit();
  }
})();
</script>
