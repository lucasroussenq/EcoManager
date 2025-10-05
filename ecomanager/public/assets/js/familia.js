async function carregarMinhasFamilias() {
  const sel = document.getElementById("familia");
  if (!sel) return;

  const r = await apiFetch('/familias/get.php');
  const j = await r.json().catch(async ()=>({ok:false}));
  if (!j.ok) return;

  sel.innerHTML = `<option value="">(Pessoal)</option>`;
  for (const f of j.data || []) {
    const o = document.createElement("option");
    o.value = f.id_familia;
    o.textContent = f.nome;
    sel.appendChild(o);
  }
}

async function criarFamilia(nome){
  const r = await apiFetch('/familias/create.php', {
    method:'POST',
    body: new URLSearchParams({ nome })
  });
  return r.json();
}
async function entrarFamilia(id_familia){
  const r = await apiFetch('/familias/entrar.php', {
    method:'POST',
    body: new URLSearchParams({ id_familia })
  });
  return r.json();
}
document.addEventListener("DOMContentLoaded", carregarMinhasFamilias);
