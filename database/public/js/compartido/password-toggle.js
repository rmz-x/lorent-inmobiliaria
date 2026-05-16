function initPasswordToggles(root = document) {
    root.querySelectorAll('.password-wrapper').forEach(wrapper => {
        if (wrapper.dataset.toggleReady === '1') return;

        const input = wrapper.querySelector('input[type="password"], input[type="text"]');
        const button = wrapper.querySelector('.toggle-password');
        const icon = wrapper.querySelector('.eye-icon');

        if (!input || !button || !icon) return;

        wrapper.dataset.toggleReady = '1';

        const updateValueState = () => {
            wrapper.classList.toggle('has-value', input.value.length > 0);
        };

        const updateIconState = () => {
            const isVisible = input.type === 'text';
            button.setAttribute('aria-label', isVisible ? 'Ocultar contraseña' : 'Mostrar contraseña');
            icon.replaceChildren();
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('viewBox', '0 0 24 24');
            svg.setAttribute('aria-hidden', 'true');
            svg.innerHTML = isVisible
                ? '<path fill="none" stroke="currentColor" stroke-width="2" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle fill="none" stroke="currentColor" stroke-width="2" cx="12" cy="12" r="3"/>'
                : '<path fill="none" stroke="currentColor" stroke-width="2" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle fill="none" stroke="currentColor" stroke-width="2" cx="12" cy="12" r="3"/><line x1="2" y1="22" x2="22" y2="2" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>';
            icon.appendChild(svg);
        };

        button.addEventListener('click', event => {
            event.preventDefault();
            input.type = input.type === 'password' ? 'text' : 'password';
            updateIconState();
            input.focus();
        });

        input.addEventListener('input', updateValueState);
        input.addEventListener('focus', () => wrapper.classList.add('focus'));
        input.addEventListener('blur', () => wrapper.classList.remove('focus'));

        updateValueState();
        updateIconState();
    });
}

function resetPasswordWrappers(root = document) {
    const wrappers = (root && root.querySelectorAll)
        ? root.querySelectorAll('.password-wrapper')
        : document.querySelectorAll('.password-wrapper');

    wrappers.forEach(wrapper => {
        const input = wrapper.querySelector('input[type="password"], input[type="text"]');
        if (!input) return;

        input.type = 'password';
        wrapper.classList.remove('has-value', 'focus');

        const button = wrapper.querySelector('.toggle-password');
        const icon = wrapper.querySelector('.eye-icon');
        if (button) button.setAttribute('aria-label', 'Mostrar contraseña');
        if (icon) {
            icon.replaceChildren();
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('viewBox', '0 0 24 24');
            svg.setAttribute('aria-hidden', 'true');
            svg.innerHTML = '<path fill="none" stroke="currentColor" stroke-width="2" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle fill="none" stroke="currentColor" stroke-width="2" cx="12" cy="12" r="3"/><line x1="2" y1="22" x2="22" y2="2" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>';
            icon.appendChild(svg);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => initPasswordToggles());
