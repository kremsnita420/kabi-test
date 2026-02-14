// import "@scss/style.scss";
import { initProductGallery } from "@js/modules/products.js";
import { initNavToggle } from "@js/modules/nav.js";

document.addEventListener("DOMContentLoaded", () => {
  initNavToggle();
  initProductGallery();
});