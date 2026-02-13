// resources/js/modules/nav.js
export const initNavToggle = () => {
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
        isOpen() ? close() : open();
    });

    closeBtn?.addEventListener("click", (e) => {
        e.preventDefault();
        close();
    });


    // Close when clicking outside the panel (on the backdrop)
    overlay.addEventListener("click", (e) => {
        if (e.target === overlay) close(); // click on dark backdrop
    });

    // Close when clicking a link
    overlay.addEventListener("click", (e) => {
        const a = e.target.closest("a");
        if (a) close();
    });

    // Close on ESC
    document.addEventListener("keydown", (e) => {
        if (!isOpen()) return;
        if (e.key === "Escape") close();
    });

    // Reset when switching to desktop width
    const mq = window.matchMedia("(max-width: 500px)");
    const handleMq = () => {
        if (!mq.matches) close();
    };
    if (typeof mq.addEventListener === "function") mq.addEventListener("change", handleMq);
    else mq.addEventListener(handleMq);
};
