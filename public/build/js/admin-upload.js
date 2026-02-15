function d(e){return document.getElementById(e)}function p(e){if(Math.abs(e)<1024)return e+" B";const t=["KB","MB","GB"];let s=-1;do e/=1024,++s;while(Math.abs(e)>=1024&&s<t.length-1);return e.toFixed(1)+" "+t[s]}async function g(e,r){const t=new FormData;t.append("image",r);const s=await fetch(e,{method:"POST",body:t}),a=await s.json().catch(()=>null);if(!s.ok||!a||!a.ok){const i=a&&a.error?a.error:`HTTP ${s.status}`;throw new Error(i)}return a}function h(e,r){var a,i;const t=((a=r.variants)==null?void 0:a.hero)||{},s=((i=r.variants)==null?void 0:i.thumb)||{};e.innerHTML=`
    <div class="upload-result__card">
      <h2>✅ Naloženo</h2>
      <p><strong>Slug:</strong> ${r.slug}</p>
      <p><strong>Index:</strong> ${r.index}</p>

      <div class="upload-result__previews">
        <div class="upload-result__preview">
          <h3>Hero</h3>
          <picture>
            ${t.webp?`<source type="image/webp" srcset="${t.webp} 1x, ${t.webp2||t.webp} 2x">`:""}
            <img src="${t.jpg||""}" alt="" loading="lazy" width="420" height="280">
          </picture>
          <code>${t.jpg||""}</code>
        </div>

        <div class="upload-result__preview">
          <h3>Thumb</h3>
          <picture>
            ${s.webp?`<source type="image/webp" srcset="${s.webp} 1x, ${s.webp2||s.webp} 2x">`:""}
            <img src="${s.jpg||""}" alt="" loading="lazy" width="140" height="140">
          </picture>
          <code>${s.jpg||""}</code>
        </div>
      </div>

      <details class="upload-result__details">
        <summary>Pokaži JSON</summary>
        <pre>${c(JSON.stringify(r,null,2))}</pre>
      </details>
    </div>
  `}function c(e){return String(e).replaceAll("&","&amp;").replaceAll("<","&lt;").replaceAll(">","&gt;")}function v(){const e=d("dropzone"),r=d("fileInput"),t=d("uploadResult");if(!e||!r||!t)return;const s=e.dataset.endpoint,a=()=>r.click();e.addEventListener("click",a),e.addEventListener("dragover",n=>{n.preventDefault(),e.classList.add("is-dragover")}),e.addEventListener("dragleave",()=>{e.classList.remove("is-dragover")}),e.addEventListener("drop",async n=>{var l,u;n.preventDefault(),e.classList.remove("is-dragover");const o=(u=(l=n.dataTransfer)==null?void 0:l.files)==null?void 0:u[0];o&&await i(o)}),r.addEventListener("change",async()=>{var o;const n=(o=r.files)==null?void 0:o[0];n&&(await i(n),r.value="")});async function i(n){if(!s)return;if(!["image/jpeg","image/png"].includes(n.type)){t.innerHTML='<div class="upload-result__error">❌ Dovoljeni so samo JPG/PNG.</div>';return}e.classList.add("is-loading"),t.innerHTML=`<div class="upload-result__loading">⏳ Nalagam <strong>${c(n.name)}</strong> (${p(n.size)}) ...</div>`;try{const l=await g(s,n);h(t,l)}catch(l){t.innerHTML=`<div class="upload-result__error">❌ ${c(l.message||"Napaka")}</div>`}finally{e.classList.remove("is-loading")}}}v();
