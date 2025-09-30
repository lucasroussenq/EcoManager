async function listaLancamentos(yyyymm){
  const q = yyyymm ? `?yyyymm=${yyyymm}` : '';
  const r = await fetch('../php/get_lancamento.php'+q,{});
  const j = await r.json();
  return j.data || [];
}
async function getLancamento(id){
  const r = await fetch('../php/get_lancamento.php?id='+id,{});
  const j = await r.json();
  return j.data;
}
async function listaCategorias(tipo){
  const q = tipo ? `?tipo=${tipo}` : '';
  const r = await fetch('../php/get_categorias.php'+q,{});
  const j = await r.json();
  return j.data || [];
}
