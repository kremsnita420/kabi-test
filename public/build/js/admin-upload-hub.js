function u(l){return document.getElementById(l)}function p(l){return String(l).replaceAll("&","&amp;").replaceAll("<","&lt;").replaceAll(">","&gt;")}async function b(l,t){const e=new FormData;e.append("image",t);const s=await fetch(l,{method:"POST",body:e}),i=await s.json().catch(()=>null);if(!s.ok||!i||!i.ok){const d=i&&i.error?i.error:`HTTP ${s.status}`;throw new Error(d)}return i}function L(l,t){var i,d;const e=((i=t.variants)==null?void 0:i.hero)||{},s=((d=t.variants)==null?void 0:d.thumb)||{};l.innerHTML=`
    <div class="upload-result__card">
      <h2>✅ Naloženo</h2>
      <p><strong>Izdelek:</strong> ${p(t.slug)} | <strong>Index:</strong> ${t.index}</p>

      <div class="upload-result__previews">
        <div class="upload-result__preview">
          <h3>Hero</h3>
          <picture>
            ${e.webp?`<source type="image/webp" srcset="${e.webp} 1x, ${e.webp2||e.webp} 2x">`:""}
            <img src="${e.jpg||""}" alt="" loading="lazy" width="420" height="280">
          </picture>
          <code>${p(e.jpg||"")}</code>
        </div>

        <div class="upload-result__preview">
          <h3>Thumb</h3>
          <picture>
            ${s.webp?`<source type="image/webp" srcset="${s.webp} 1x, ${s.webp2||s.webp} 2x">`:""}
            <img src="${s.jpg||""}" alt="" loading="lazy" width="140" height="140">
          </picture>
          <code>${p(s.jpg||"")}</code>
        </div>
      </div>

      <details class="upload-result__details">
        <summary>Pokaži JSON</summary>
        <pre>${p(JSON.stringify(t,null,2))}</pre>
      </details>
    </div>
  `}function y(){const l=u("productSearch"),t=u("productSelect"),e=u("dropzone"),s=u("fileInput"),i=u("dropzoneHint"),d=u("uploadTarget"),g=u("uploadResult");if(!l||!t||!e||!s||!i||!d||!g)return;const h=e.dataset.endpointBase||"/admin/upload";let c="";function v(n){var f;c=n||"";const a=c!=="";e.classList.toggle("is-disabled",!a);const r=a?((f=t.options[t.selectedIndex])==null?void 0:f.textContent)||c:"—";d.querySelector(".admin-upload__target-name").textContent=r;const o=a?` → /assets/product-images/${c}/`:"";d.querySelector(".admin-upload__target-path").textContent=o,i.textContent=a?`Upload v: /assets/product-images/${c}/`:"Najprej izberi izdelek."}t.options.length?(t.selectedIndex=0,v(t.value)):v(""),l.addEventListener("input",()=>{const n=l.value.trim().toLowerCase();for(const r of t.options){const o=r.textContent.toLowerCase();r.hidden=n!==""&&!o.includes(n)}const a=t.options[t.selectedIndex];if(a&&a.hidden){const r=Array.from(t.options).find(o=>!o.hidden);r?(t.value=r.value,v(t.value)):v("")}}),t.addEventListener("change",()=>v(t.value));function w(){e.classList.contains("is-disabled")||s.click()}e.addEventListener("click",w),e.addEventListener("dragover",n=>{e.classList.contains("is-disabled")||(n.preventDefault(),e.classList.add("is-dragover"))}),e.addEventListener("dragleave",()=>e.classList.remove("is-dragover")),e.addEventListener("drop",async n=>{var r,o;if(e.classList.contains("is-disabled"))return;n.preventDefault(),e.classList.remove("is-dragover");const a=(o=(r=n.dataTransfer)==null?void 0:r.files)==null?void 0:o[0];a&&await m(a)}),s.addEventListener("change",async()=>{var a;const n=(a=s.files)==null?void 0:a[0];n&&(await m(n),s.value="")});async function m(n){if(!c)return;if(!["image/jpeg","image/png"].includes(n.type)){g.innerHTML='<div class="upload-result__error">❌ Dovoljeni so samo JPG/PNG.</div>';return}const r=`${h}/${c}`;e.classList.add("is-loading"),g.innerHTML=`<div class="upload-result__loading">⏳ Nalagam <strong>${p(n.name)}</strong> ...</div>`;try{const o=await b(r,n);L(g,o)}catch(o){g.innerHTML=`<div class="upload-result__error">❌ ${p(o.message||"Napaka")}</div>`}finally{e.classList.remove("is-loading")}}}y();
