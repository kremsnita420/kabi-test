function $(id) {
  return document.getElementById(id);
}

function escapeHtml(s) {
  return String(s)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;");
}

async function uploadFile(endpoint, file) {
  const form = new FormData();
  form.append("image", file);

  const res = await fetch(endpoint, { method: "POST", body: form });
  const json = await res.json().catch(() => null);

  if (!res.ok || !json || !json.ok) {
    const msg = (json && json.error) ? json.error : `HTTP ${res.status}`;
    throw new Error(msg);
  }
  return json;
}

function renderResult(container, data) {
  const hero = data.variants?.hero || {};
  const thumb = data.variants?.thumb || {};

  container.innerHTML = `
    <div class="upload-result__card">
      <h2>✅ Naloženo</h2>
      <p><strong>Izdelek:</strong> ${escapeHtml(data.slug)} | <strong>Index:</strong> ${data.index}</p>

      <div class="upload-result__previews">
        <div class="upload-result__preview">
          <h3>Hero</h3>
          <picture>
            ${hero.webp ? `<source type="image/webp" srcset="${hero.webp} 1x, ${hero.webp2 || hero.webp} 2x">` : ""}
            <img src="${hero.jpg || ""}" alt="" loading="lazy" width="420" height="280">
          </picture>
          <code>${escapeHtml(hero.jpg || "")}</code>
        </div>

        <div class="upload-result__preview">
          <h3>Thumb</h3>
          <picture>
            ${thumb.webp ? `<source type="image/webp" srcset="${thumb.webp} 1x, ${thumb.webp2 || thumb.webp} 2x">` : ""}
            <img src="${thumb.jpg || ""}" alt="" loading="lazy" width="140" height="140">
          </picture>
          <code>${escapeHtml(thumb.jpg || "")}</code>
        </div>
      </div>

      <details class="upload-result__details">
        <summary>Pokaži JSON</summary>
        <pre>${escapeHtml(JSON.stringify(data, null, 2))}</pre>
      </details>
    </div>
  `;
}

export function initAdminUploadHub() {
  const search = $("productSearch");
  const select = $("productSelect");
  const dz = $("dropzone");
  const input = $("fileInput");
  const hint = $("dropzoneHint");
  const target = $("uploadTarget");
  const result = $("uploadResult");

  if (!search || !select || !dz || !input || !hint || !target || !result) return;

  const endpointBase = dz.dataset.endpointBase || "/admin/upload";
  let selectedSlug = "";

  function setSelectedSlug(slug) {
    selectedSlug = slug || "";
    const enabled = selectedSlug !== "";

    dz.classList.toggle("is-disabled", !enabled);

    const nameText = enabled ? select.options[select.selectedIndex]?.textContent || selectedSlug : "—";
    target.querySelector(".admin-upload__target-name").textContent = nameText;

    const path = enabled ? ` → /assets/product-images/${selectedSlug}/` : "";
    target.querySelector(".admin-upload__target-path").textContent = path;

    hint.textContent = enabled ? `Upload v: /assets/product-images/${selectedSlug}/` : "Najprej izberi izdelek.";
  }

  // default pick first
  if (select.options.length) {
    select.selectedIndex = 0;
    setSelectedSlug(select.value);
  } else {
    setSelectedSlug("");
  }

  // search filter
  search.addEventListener("input", () => {
    const q = search.value.trim().toLowerCase();

    for (const opt of select.options) {
      const txt = opt.textContent.toLowerCase();
      opt.hidden = q !== "" && !txt.includes(q);
    }

    // if current selection becomes hidden, pick first visible
    const current = select.options[select.selectedIndex];
    if (current && current.hidden) {
      const firstVisible = Array.from(select.options).find((o) => !o.hidden);
      if (firstVisible) {
        select.value = firstVisible.value;
        setSelectedSlug(select.value);
      } else {
        setSelectedSlug("");
      }
    }
  });

  select.addEventListener("change", () => setSelectedSlug(select.value));

  function pickFile() {
    if (dz.classList.contains("is-disabled")) return;
    input.click();
  }

  dz.addEventListener("click", pickFile);

  dz.addEventListener("dragover", (e) => {
    if (dz.classList.contains("is-disabled")) return;
    e.preventDefault();
    dz.classList.add("is-dragover");
  });

  dz.addEventListener("dragleave", () => dz.classList.remove("is-dragover"));

  dz.addEventListener("drop", async (e) => {
    if (dz.classList.contains("is-disabled")) return;
    e.preventDefault();
    dz.classList.remove("is-dragover");
    const file = e.dataTransfer?.files?.[0];
    if (!file) return;
    await handle(file);
  });

  input.addEventListener("change", async () => {
    const file = input.files?.[0];
    if (!file) return;
    await handle(file);
    input.value = "";
  });

  async function handle(file) {
    if (!selectedSlug) return;

    const okTypes = ["image/jpeg", "image/png"];
    if (!okTypes.includes(file.type)) {
      result.innerHTML = `<div class="upload-result__error">❌ Dovoljeni so samo JPG/PNG.</div>`;
      return;
    }

    const endpoint = `${endpointBase}/${selectedSlug}`;

    dz.classList.add("is-loading");
    result.innerHTML = `<div class="upload-result__loading">⏳ Nalagam <strong>${escapeHtml(file.name)}</strong> ...</div>`;

    try {
      const data = await uploadFile(endpoint, file);
      renderResult(result, data);
    } catch (err) {
      result.innerHTML = `<div class="upload-result__error">❌ ${escapeHtml(err.message || "Napaka")}</div>`;
    } finally {
      dz.classList.remove("is-loading");
    }
  }
}

initAdminUploadHub();
