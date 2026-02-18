/* Simple i18n loader: loads /assets/i18n/{lang}.json and applies translations
   Usage: add data-i18n="key" to elements. To pass a username or other var, add
   data-i18n-<name> attributes (for example data-i18n-username="Alice").
   A <select id="langSelect"> in the header lets user switch langs (stored in localStorage).
*/
(function(){
  function detectLang(){
    const stored = localStorage.getItem('lang');
    if (stored) return stored;
    const nav = (navigator.languages && navigator.languages[0]) || navigator.language || 'en';
    return nav.split('-')[0];
  }

  async function load(lang){
    try{
      const res = await fetch(`/assets/i18n/${lang}.json`);
      if (!res.ok) throw new Error('no resource');
      window.i18n = await res.json();
    }catch(e){
      if (lang !== 'en') return load('en');
      window.i18n = {};
    }
    apply();
    setLangSelector(lang);
  }

  function t(key, vars){
    const val = (window.i18n && window.i18n[key]) || key;
    if (!vars) return val;
    return val.replace(/\{([^}]+)\}/g, (_, name)=> (vars[name] !== undefined ? vars[name] : `{${name}}`));
  }

  function apply(){
    document.querySelectorAll('[data-i18n]').forEach(el=>{
      const key = el.getAttribute('data-i18n');
      const vars = {};
      Array.from(el.attributes).forEach(attr=>{
        if (attr.name.startsWith('data-i18n-') && attr.name !== 'data-i18n'){
          const varName = attr.name.slice('data-i18n-'.length);
          vars[varName] = attr.value;
        }
      });
      const text = t(key, vars);
      const tag = el.tagName;
      if (tag === 'INPUT'){
        const type = (el.getAttribute('type')||'').toLowerCase();
        if (el.hasAttribute('placeholder')){
          el.setAttribute('placeholder', text);
        } else if (type === 'button' || type === 'submit' || type === 'reset') {
          el.value = text;
        } // otherwise skip changing .value so we don't overwrite user input
      } else if (tag === 'TEXTAREA') {
        if (el.hasAttribute('placeholder')) el.setAttribute('placeholder', text);
      } else if (tag === 'BUTTON') {
        el.textContent = text;
      } else if (tag === 'SELECT') {
        // don't change select value; option tags can have their own data-i18n
      } else {
        el.textContent = text;
      }
    });
  }

  function setLangSelector(lang){
    const sel = document.getElementById('langSelect');
    if (!sel) return;
    sel.value = lang;
  }

  // Init on DOM ready
  document.addEventListener('DOMContentLoaded', ()=>{
    const lang = detectLang();
    load(lang);

    document.addEventListener('click', (e)=>{
      const el = e.target.closest('[data-lang]');
      if (el){
        const l = el.getAttribute('data-lang');
        if (l){ localStorage.setItem('lang', l); load(l); }
      }
    });

    const sel = document.getElementById('langSelect');
    if (sel){
      sel.addEventListener('change', ()=>{
        const v = sel.value; localStorage.setItem('lang', v); load(v);
      });
    }
  });

  // expose t for other scripts
  window.i18n_t = t;
})();
