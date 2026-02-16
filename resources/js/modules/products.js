// resources/js/modules/products.js

import Swiper from "swiper";
import { Navigation, Thumbs, Keyboard } from "swiper/modules";
import "swiper/css";

export function initProductGallery() {
  const root = document.querySelector("[data-product-gallery]");
  if (!root) return;

  const mainEl = root.querySelector("[data-gallery-main]");
  const thumbsEl = root.querySelector("[data-gallery-thumbs]");
  if (!mainEl || !thumbsEl) return;

  const prevEl = root.querySelector("[data-gallery-prev]");
  const nextEl = root.querySelector("[data-gallery-next]");

  // Thumbs slider
  const thumbs = new Swiper(thumbsEl, {
    modules: [Thumbs],
    slidesPerView: "auto",
    spaceBetween: 10,
    watchSlidesProgress: true,
    freeMode: true,

    // Helps Swiper when layout changes (grid, fonts, images)
    watchOverflow: true,
    observer: true,
    observeParents: true,
    resizeObserver: true,
  });

  // Main slider
  const main = new Swiper(mainEl, {
    modules: [Navigation, Thumbs, Keyboard],
    slidesPerView: 1,
    spaceBetween: 0,

    keyboard: { enabled: true },

    thumbs: { swiper: thumbs },

    navigation: {
      prevEl,
      nextEl,
    },

    // Helps Swiper when layout changes (grid, fonts, images)
    watchOverflow: true,
    observer: true,
    observeParents: true,
    resizeObserver: true,
  });

  // Mark ready (optional, for CSS hooks)
  mainEl.classList.add("is-ready");
  thumbsEl.classList.add("is-ready");

  // ✅ Critical: force Swiper to recalc once layout is stable
  // (fixes weird huge widths like 1.6e+07px)
  const forceUpdate = () => {
    try {
      thumbs.update();
      main.update();
    } catch (e) {
      // no-op
    }
  };

  // First tick + after full load
  requestAnimationFrame(forceUpdate);
  window.addEventListener("load", forceUpdate, { once: true });

  // Also update when all images inside the gallery finish loading
  const imgs = root.querySelectorAll("img");
  let pending = 0;

  imgs.forEach((img) => {
    if (!img.complete) {
      pending++;
      img.addEventListener(
        "load",
        () => {
          pending--;
          // wait a tick so DOM sizes apply
          requestAnimationFrame(forceUpdate);
        },
        { once: true }
      );
      img.addEventListener(
        "error",
        () => {
          pending--;
          requestAnimationFrame(forceUpdate);
        },
        { once: true }
      );
    }
  });

  // Last resort: update after a short delay (fonts/layout shifts)
  setTimeout(forceUpdate, 250);
  setTimeout(forceUpdate, 800);
}
