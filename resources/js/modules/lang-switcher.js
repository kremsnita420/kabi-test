
export function initLangSwitcher() {
  const root = document.querySelector('[data-lang-dd]');
  if (!root) return;

  const btn = root.querySelector('[data-lang-dd-btn]');
  const menu = root.querySelector('[data-lang-dd-menu]');
  if (!btn || !menu) return;

  const open = () => {
    root.classList.add('is-open');
    btn.setAttribute('aria-expanded', 'true');
    menu.removeAttribute('hidden');
  };

  const close = () => {
    root.classList.remove('is-open');
    btn.setAttribute('aria-expanded', 'false');
    menu.setAttribute('hidden', 'hidden');
  };

  const toggle = () => (root.classList.contains('is-open') ? close() : open());

  // Start closed
  close();

  btn.addEventListener('click', (e) => {
    e.preventDefault();
    toggle();
  });

  // Persist language choice via cookie on click (including default "sl")
  root.addEventListener('click', (e) => {
    const a = e.target && e.target.closest ? e.target.closest('a[data-lang]') : null;
    if (!a) return;
    const lang = a.getAttribute('data-lang');
    if (!lang) return;
    // 1 year
    document.cookie = `kabi_lang=${encodeURIComponent(lang)}; path=/; max-age=${60 * 60 * 24 * 365}; SameSite=Lax`;
  });

  // Close on outside click
  document.addEventListener('click', (e) => {
    if (!root.contains(e.target)) close();
  });

  // Keyboard support
  document.addEventListener('keydown', (e) => {
    if (!root.classList.contains('is-open')) return;
    if (e.key === 'Escape') {
      close();
      btn.focus();
    }
  });Z
}

