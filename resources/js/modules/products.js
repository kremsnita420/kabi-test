import { qs } from "../core/dom.js";
import { log } from "../utils/logger.js";

export const initProducts = () => {
  // Example: determine page type by presence of elements.
  const grid = qs(".grid");
  if (grid) {
    log("Product list loaded");
    return;
  }

  const title = qs("h1");
  if (title) {
    log("Product single loaded");
  }
};
