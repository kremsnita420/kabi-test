export function initProductGallery() {
  const mainImg = document.getElementById("productMainImage");
  if (!mainImg) return;

  const picture = mainImg.closest("picture");
  const webpSource = picture ? picture.querySelector('source[type="image/webp"]') : null;

  const thumbs = document.querySelectorAll(".product-gallery__thumb");
  if (!thumbs.length) return;

  thumbs.forEach((btn) => {
    btn.addEventListener("click", () => {
      const heroJpg = btn.dataset.heroJpg;
      if (!heroJpg) return;

      const heroJpg2 = btn.dataset.heroJpg2 || "";
      const heroWebp = btn.dataset.heroWebp || "";
      const heroWebp2 = btn.dataset.heroWebp2 || "";

      // Update main <img>
      mainImg.src = heroJpg;

      if (heroJpg2) {
        mainImg.srcset = `${heroJpg} 1x, ${heroJpg2} 2x`;
      } else {
        mainImg.removeAttribute("srcset");
      }

      // Update webp <source> if present
      if (webpSource) {
        if (heroWebp) {
          const webp2 = heroWebp2 || heroWebp;
          webpSource.srcset = `${heroWebp} 1x, ${webp2} 2x`;
        } else {
          // no webp for this image (fallback to jpg)
          webpSource.removeAttribute("srcset");
        }
      }

      // Active state
      thumbs.forEach((b) => b.classList.remove("is-active"));
      btn.classList.add("is-active");
    });
  });
}
