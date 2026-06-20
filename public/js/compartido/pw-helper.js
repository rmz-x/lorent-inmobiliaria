(function(){
  // pw-helper.js — Ayudante de requisitos de contraseña.
  // Inicializa helpers solo en inputs con: .pw-input[data-pw-enable="1"]
  // Añade soporte para inputs añadidos dinámicamente mediante MutationObserver.

  const rules = {
    length: pwd => pwd.length >= 8,
    upper: pwd => /[A-Z]/.test(pwd),
    number: pwd => /\d/.test(pwd),
    special: pwd => /[^A-Za-z0-9]/.test(pwd)
  };

  const SVG = {
    ok: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17l-5-5" stroke="#16A34A" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    no: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="#dc2626" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>'
  };

  function createHelper(){
    const d = document.createElement('div');
    d.className = 'pw-helper pw-hidden';
    d.setAttribute('role','status');
    d.setAttribute('aria-live','polite');
    d.setAttribute('aria-hidden','true');
    d.innerHTML = '\n      <ul class="pw-list">\n        <li data-rule="length" class="pw-item"><span class="pw-icon" aria-hidden="true"></span><span class="pw-text">Mínimo 8 caracteres</span></li>\n        <li data-rule="upper" class="pw-item"><span class="pw-icon" aria-hidden="true"></span><span class="pw-text">Al menos una letra mayúscula (A-Z)</span></li>\n        <li data-rule="number" class="pw-item"><span class="pw-icon" aria-hidden="true"></span><span class="pw-text">Al menos un número (0-9)</span></li>\n        <li data-rule="special" class="pw-item"><span class="pw-icon" aria-hidden="true"></span><span class="pw-text">Al menos un carácter especial (@ ! # ?)</span></li>\n      </ul>';
    return d;
  }

  function show(helper){
    if(!helper) return;
    helper.setAttribute('aria-hidden','false');
    helper.classList.add('pw-visible');
    helper.classList.remove('pw-hidden');
  }
  function hide(helper){
    if(!helper) return;
    helper.setAttribute('aria-hidden','true');
    helper.classList.remove('pw-visible');
    helper.classList.add('pw-hidden');
  }

  function initializeInput(input, idx){
    if(!input || input.dataset._pwInit) return;
    input.dataset._pwInit = '1';

    const wrapper = input.closest('.pw-wrapper') || input.closest('.password-wrapper') || input.closest('.input-group');
    const helper = createHelper();
    const hid = 'pw-helper-' + (input.id || idx || Math.floor(Math.random()*10000));
    helper.id = hid;
    try{ input.dataset.pwHelperId = hid; }catch(e){}
    try{ if(wrapper) wrapper.dataset.pwHelperId = hid; }catch(e){}

    if(wrapper && wrapper.parentNode){
      wrapper.parentNode.insertBefore(helper, wrapper.nextSibling);
    } else if(input.parentNode){
      try{ input.insertAdjacentElement('afterend', helper); }catch(e){ input.parentNode.insertBefore(helper, input.nextSibling); }
    }

    function update(pwd){
      Object.keys(rules).forEach(function(ruleKey){
        const satisfied = rules[ruleKey](pwd);
        const li = helper.querySelector('.pw-item[data-rule="'+ruleKey+'"]');
        if(!li) return;
        const icon = li.querySelector('.pw-icon');
        if(satisfied){
          li.classList.add('satisfied');
          if(icon) icon.innerHTML = SVG.ok;
        } else {
          li.classList.remove('satisfied');
          if(icon) icon.innerHTML = SVG.no;
        }
      });
    }

    input.addEventListener('focus', function(){ update(input.value || ''); show(helper); });
    input.addEventListener('click', function(){ update(input.value || ''); show(helper); });
    if(wrapper) wrapper.addEventListener('pointerdown', function(){ update(input.value || ''); show(helper); });

    var toggle = null;
    try{
      toggle = (wrapper && wrapper.querySelector) ? wrapper.querySelector('.toggle-password') : input.parentNode && input.parentNode.querySelector && input.parentNode.querySelector('.toggle-password');
    }catch(e){ toggle = null; }
    if(toggle){
      toggle.addEventListener('pointerdown', function(ev){
        update(input.value || '');
        show(helper);
        setTimeout(function(){ try{ input.focus(); }catch(e){} }, 10);
      });
    }

    input.addEventListener('input', function(e){ update(e.target.value || ''); show(helper); });
    update(input.value || '');

    input.addEventListener('blur', function(){
      setTimeout(function(){
        const active = document.activeElement;
        if(active === input) return;
        if(helper.contains(active)) return;
        hide(helper);
      }, 150);
    });
  }

  function processInputs(){
    const inputs = Array.from(document.querySelectorAll('.pw-input[data-pw-enable="1"]'));
    inputs.forEach(function(input, idx){ initializeInput(input, idx); });
  }

  function handleFocusIn(){
    document.addEventListener('focusin', function(){
      const active = document.activeElement;
      const inputs = Array.from(document.querySelectorAll('.pw-input[data-pw-enable="1"]'));
      inputs.forEach(function(input){
        const hid = input.dataset && input.dataset.pwHelperId;
        let helper = hid ? document.getElementById(hid) : null;
        if(!helper) helper = input.nextElementSibling && input.nextElementSibling.classList && input.nextElementSibling.classList.contains('pw-helper') ? input.nextElementSibling : null;
        if(!helper) return;
        const wrapper = input.closest('.pw-wrapper') || input.closest('.password-wrapper') || input.closest('.input-group');
        if(active === input || (wrapper && wrapper.contains(active)) || helper.contains(active)){
          // update current state
          const ev = { target: input };
          try{ // reuse update by triggering input handlers indirectly
            const evt = new Event('input', { bubbles: true });
            input.dispatchEvent(evt);
          }catch(e){}
          show(helper);
        } else {
          hide(helper);
        }
      });
    });
  }

  function observeMutations(){
    if(window.__pwHelperObserverInitialized) return; window.__pwHelperObserverInitialized = true;
    try{
      const observer = new MutationObserver(function(mutations){
        let found = false;
        mutations.forEach(function(m){
          if(m.addedNodes && m.addedNodes.length){
            m.addedNodes.forEach(function(n){
              if(n.nodeType !== 1) return;
              if(n.matches && n.matches('.pw-input[data-pw-enable="1"]')) found = true;
              if(n.querySelector && n.querySelector('.pw-input[data-pw-enable="1"]')) found = true;
            });
          }
        });
        if(found) processInputs();
      });
      observer.observe(document.documentElement || document.body, { childList:true, subtree:true });
    }catch(e){ /* silently ignore */ }
  }

  function init(){
    try{
      processInputs();
      handleFocusIn();
      observeMutations();
    }catch(err){ console.error('pw-helper init error', err); }
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', init);
  } else init();
})();
