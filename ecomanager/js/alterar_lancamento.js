document.addEventListener("DOMContentLoaded", async () => {
  const searchParams = new URLSearchParams(window.location.search);
  const id = searchParams.get("id");
  document.getElementById("id").value = id;

  const l = await getLancamento(id);
  document.getElementById("data_mov").value = l.data_mov;
  document.getElementById("descricao").value = l.descricao || '';
  document.getElementById("tipo").value = l.tipo;

  await carregaCategorias(l.tipo);
  document.getElementById("id_categoria").value = l.id_categoria;
  document.getElementById("valor").value = Number(l.valor).toFixed(2);
  document.getElementById("id_familia").value = l.id_familia || '';

  document.getElementById("tipo").addEventListener('change', async (e)=> {
    await carregaCategorias(e.target.value);
  });
});

async function carregaCategorias(tipo){
  const list = await listaCategorias(tipo);
  const sel = document.getElementById('id_categoria');
  sel.innerHTML = '';
  list.forEach(c => {
    const op = document.createElement('option');
    op.value = c.id; op.textContent = c.nome;
    sel.appendChild(op);
  });
}

document.getElementById("enviar").addEventListener("click", async ()=>{
  const fd = new FormData();
  fd.append("id", document.getElementById("id").value);
  fd.append("data_mov", document.getElementById("data_mov").value);
  fd.append("descricao", document.getElementById("descricao").value);
  fd.append("id_categoria", document.getElementById("id_categoria").value);
  fd.append("tipo", document.getElementById("tipo").value);
  fd.append("valor", document.getElementById("valor").value);
  fd.append("id_familia", document.getElementById("id_familia").value);

  const r = await fetch('../php/alterar_lancamento.php',{ method:'POST', body: fd });
  const j = await r.json();
  if(j.ok){ window.location.href = '../lancamento/index.html'; }
  else alert(j.msg||'Erro ao salvar');
});
