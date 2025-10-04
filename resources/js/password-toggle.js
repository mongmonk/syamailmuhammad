// Password visibility toggle script
// Finds any button with data-toggle="password" and toggles its target input type
document.addEventListener('DOMContentLoaded', () => {
  const buttons = document.querySelectorAll('[data-toggle="password"]');
  buttons.forEach((btn) => {
    const targetId = btn.getAttribute('data-target');
    if (!targetId) return;

    const input = document.getElementById(targetId);
    if (!input) return;

    const updateState = (show) => {
      input.setAttribute('type', show ? 'text' : 'password');
      const eye = btn.querySelector('[data-eye]');
      const eyeOff = btn.querySelector('[data-eye-off]');
      if (eye && eyeOff) {
        if (show) {
          eye.classList.add('hidden');
          eyeOff.classList.remove('hidden');
        } else {
          eye.classList.remove('hidden');
          eyeOff.classList.add('hidden');
        }
      }
      btn.setAttribute('aria-pressed', show ? 'true' : 'false');
      btn.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
      // maintain focus on input if desired
      input.focus({ preventScroll: true });
    };

    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const isCurrentlyPassword = input.getAttribute('type') === 'password';
      updateState(isCurrentlyPassword);
    });

    // Optional: support keyboard shortcut Alt+V when focused inside the field wrapper
    const wrapper = btn.closest('[data-password-wrapper]');
    if (wrapper) {
      wrapper.addEventListener('keydown', (ev) => {
        if ((ev.altKey || ev.metaKey) && (ev.key === 'v' || ev.key === 'V')) {
          ev.preventDefault();
          const isCurrentlyPassword = input.getAttribute('type') === 'password';
          updateState(isCurrentlyPassword);
        }
      });
    }
  });
});