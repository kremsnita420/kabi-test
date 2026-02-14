export function initProductGallery() {
  const main = document.getElementById("productMainImage");
  if (!main) return;

  const thumbs = document.querySelectorAll(".product-gallery__thumb");
  if (!thumbs.length) return;

  thumbs.forEach(btn => {
    btn.addEventListener("click", () => {
      const src = btn.dataset.img;
      if (!src) return;

      main.src = src;

      thumbs.forEach(b => b.classList.remove("is-active"));
      btn.classList.add("is-active");
    });
  });
}
