import "@scss/style.scss";
import { initNavToggle } from "@js/modules/nav.js";
import {initProductGallery} from "@js/modules/products-slider.js";
import { initAdminUploadHub } from "@js/pages/admin-upload-hub";

document.addEventListener("DOMContentLoaded", () => {
  // Global nav toggle (mobile)
  initNavToggle();

  // Product gallery (only on product page)
  if (document.body.classList.contains("page-product-single")) {
    initProductGallery();
  }

  // Admin upload hub
  if (document.body.classList.contains("page-admin-upload-hub")) {
    initAdminUploadHub();
  }

});