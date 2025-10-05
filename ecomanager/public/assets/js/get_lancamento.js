async function listaLancamentos(yyyymm){
  const q = yyyymm ? `?mes=${yyyymm}` : '';
  const r = await apiFetch(`/lancamento/list.php${q}`);
  const j = await r.json().catch(async ()=>({registros:[]}));
  return j.registros || [];
}
async function getLancamento(id){
  const r = await apiFetch(`/lancamento/item.php?id=${id}`);
  const j = await r.json().catch(async ()=>({}));
  return j.item;
}
async function listaCategorias(tipo){
  const q = tipo ? `?tipo=${encodeURIComponent(tipo)}` : '';
  const r = await apiFetch(`/categorias/list.php${q}`);
  const j = await r.json().catch(async ()=>({categorias:[]}));
  return j.categorias || [];
}
