// js/familia.js
async function carregarMinhasFamilias() {
  const sel = document.getElementById("familia");
  if (!sel) return;

  const r = await fetch("../php/get_familias.php");
  const j = await r.json();
  if (!j.ok) return;

  sel.innerHTML = `<option value="">(Pessoal)</option>`;
  for (const f of j.data) {
    const o = document.createElement("option");
    o.value = f.id_familia;
    o.textContent = f.nome;
    sel.appendChild(o);
  }
}

// criar/entrar (use em telas simples, se quiser)
async function criarFamilia(nome){
  const r = await fetch("../php/criar_familia.php", {method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({nome})});
  return await r.json();
}
async function entrarFamilia(id_familia){
  const r = await fetch("../php/entrar_familia.php", {method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({id_familia})});
  return await r.json();
}

document.addEventListener("DOMContentLoaded", carregarMinhasFamilias);
