import "@scss/style.scss";
import { initNavToggle } from "@js/modules/nav.js";
import {initProductGallery} from "@js/modules/products-slider.js";

document.addEventListener("DOMContentLoaded", () => {
  initNavToggle();
  initProductGallery();

});