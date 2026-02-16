// resources/js/modules/nav.js
export const initNavToggle = () => {
    console.log("nav init running");

  const toggle = document.querySelector(".nav-toggle");
  const overlay = document.querySelector("[data-nav]");
  const closeBtn = document.querySelector(".nav-close");

  if (!toggle || !overlay) return;

  const open = () => {
    overlay.classList.add("is-open");
    toggle.classList.add("is-open");
    toggle.setAttribute("aria-expanded", "true");
    overlay.setAttribute("aria-hidden", "false");
    document.documentElement.classList.add("nav-open");
    document.body.classList.add("nav-open");
  };

  const close = () => {
    overlay.classList.remove("is-open");
    toggle.classList.remove("is-open");
    toggle.setAttribute("aria-expanded", "false");
    overlay.setAttribute("aria-hidden", "true");
    document.documentElement.classList.remove("nav-open");
    document.body.classList.remove("nav-open");
  };

  const isOpen = () => overlay.classList.contains("is-open");

  toggle.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation(); // ✅ prevents weird bubbling if toggle is inside overlay
    isOpen() ? close() : open();
  });

  closeBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    close();
  });

  // ✅ One overlay click handler:
  overlay.addEventListener("click", (e) => {
    // 1) Click on backdrop closes
    if (e.target === overlay) {
      close();
      return;
    }

    // 2) Click on a nav link closes (but not the toggle)
    const a = e.target.closest("a");
    if (a && a !== toggle && !toggle.contains(a)) {
      close();
    }
  });

  // ESC closes
  document.addEventListener("keydown", (e) => {
    if (isOpen() && e.key === "Escape") close();
  });

  // Reset when switching to desktop width
  const mq = window.matchMedia("(max-width: 500px)");
  const handleMq = () => {
    if (!mq.matches) close();
  };

  if (typeof mq.addEventListener === "function") mq.addEventListener("change", handleMq);
  else mq.addListener(handleMq); // ✅ correct fallback
};
