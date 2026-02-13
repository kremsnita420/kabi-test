// import "@scss/style.scss";
import { initProducts } from "@js/modules/products.js";
import { initNavToggle } from "@js/modules/nav.js";

document.addEventListener("DOMContentLoaded", () => {
  initNavToggle();
  initProducts();
});