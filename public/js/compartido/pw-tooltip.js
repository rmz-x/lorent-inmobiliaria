(function(){
(function(){
  const input = document.getElementById('registerPassword');
  const tooltip = document.getElementById('pw-tooltip');
  if(!input || !tooltip) return;
  const wrapper = input.closest('.pw-wrapper');

  const rules = {
    length: pwd => pwd.length >= 8,
    upper: pwd => /[A-Z]/.test(pwd),
    number: pwd => /\d/.test(pwd),
    special: pwd => /[^A-Za-z0-9]/.test(pwd)
  };

  function update(pwd){
    Object.keys(rules).forEach(ruleKey=>{
      const satisfied = rules[ruleKey](pwd);
      const li = tooltip.querySelector(`.pw-item[data-rule="${ruleKey}"]`);
      if(!li) return;
      const icon = li.querySelector('.pw-icon');
      const text = li.querySelector('.pw-text');
        if(satisfied){
        li.classList.add('satisfied');
        if(icon) icon.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17l-5-5" stroke="#16A34A" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
      } else {
        li.classList.remove('satisfied');
        if(icon) icon.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="#dc2626" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
      }
    });
  }

  function show(){
    // move tooltip to body to avoid parent overflow clipping
    if(tooltip.parentNode !== document.body){
      document.body.appendChild(tooltip);
      tooltip.classList.add('pw-fixed');
    }
    tooltip.style.display = 'block';
    tooltip.style.opacity = '1';
    tooltip.style.pointerEvents = 'auto';

    // position tooltip above the input using viewport coordinates
    const inRect = input.getBoundingClientRect();
    // reset left/top to measure
    tooltip.style.left = '0px';
    tooltip.style.top = '0px';
    const tRect = tooltip.getBoundingClientRect();
    let left = inRect.left + (inRect.width/2) - (tRect.width/2);
    let top = inRect.top - tRect.height - 8;
    // keep inside viewport horizontally
    left = Math.max(8, Math.min(left, window.innerWidth - tRect.width - 8));
    // if not enough space above, place below
    if(top < 8){
      top = inRect.bottom + 8;
      tooltip.classList.add('pw-below');
    } else {
      tooltip.classList.remove('pw-below');
    }
    tooltip.style.left = `${left}px`;
    tooltip.style.top = `${top}px`;
  }

  function hide(){
    tooltip.style.display = 'none';
    tooltip.style.opacity = '0';
    tooltip.style.pointerEvents = 'none';
  }

  input.addEventListener('focus', (e)=>{ show(); update(input.value || ''); });
  input.addEventListener('focusin', (e)=>{ show(); update(input.value || ''); });
  input.addEventListener('click', (e)=>{ show(); update(input.value || ''); });
  input.addEventListener('input', (e)=>{ update(e.target.value || ''); });
  if(wrapper){
    wrapper.addEventListener('pointerdown', ()=>{ show(); update(input.value || ''); });
  }

  document.addEventListener('click', (e)=>{
    if(e.target === input || tooltip.contains(e.target)) return;
    hide();
  });

  document.addEventListener('focusin', (e)=>{
    const active = document.activeElement;
    if(active === input) return;
    if(tooltip.contains(active)) return;
    hide();
  });

  update(input.value || '');
})();
