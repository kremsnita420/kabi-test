import "../scss/style.scss";
// JS entry (SOC-friendly)
import { initProducts } from "./modules/products.js";

document.addEventListener("DOMContentLoaded", () => {
  initProducts();
});
