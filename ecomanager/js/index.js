let pagina = 1, porPagina = 20;

function money(v){ return (Number(v)||0).toLocaleString('pt-BR',{style:'currency',currency:'BRL'}); }

document.addEventListener("DOMContentLoaded", async () => {
  // mês padrão (YYYYMM)
  document.getElementById("yyyymm").value = new Date().toISOString().slice(0,7).replace('-','');

  // preencher combo de categorias (todas no início)
  const cats = await listaCategorias('');
  const sel = document.getElementById('f_categoria');
  cats.forEach(c => {
    const op = document.createElement('option');
    op.value = c.id; op.textContent = c.nome;
    sel.appendChild(op);
  });

  document.getElementById("novo").addEventListener('click', () => {
    window.location.href = '../lancamento/inserir_lancamento.htm';
  });
  document.getElementById("filtrar").addEventListener('click', ()=>{ pagina=1; lista(); });
  document.getElementById("limpar").addEventListener('click', ()=>{
    document.getElementById("f_tipo").value = '';
    document.getElementById("f_categoria").value = '';
    pagina = 1; lista();
  });
  document.getElementById("prev").addEventListener('click', ()=>{ if(pagina>1){ pagina--; lista(); }});
  document.getElementById("next").addEventListener('click', ()=>{ pagina++; lista(); });

  await lista();
});

async function lista(){
  // monta query com filtros (compatível com seu get_lancamento.php avançado; se ainda não usar paginação, ignora)
  const yyyymm = document.getElementById("yyyymm").value.trim();
  const tipo = document.getElementById("f_tipo").value;
  const cat  = document.getElementById("f_categoria").value;

  let q = `?pagina=${pagina}&por_pagina=${porPagina}`;
  if(yyyymm) q += `&yyyymm=${encodeURIComponent(yyyymm)}`;
  if(tipo)   q += `&tipo=${encodeURIComponent(tipo)}`;
  if(cat)    q += `&categoria=${encodeURIComponent(cat)}`;

  const r = await fetch('../php/get_lancamento.php'+q,{});
  const j = await r.json();
  const lista = j.data || [];

  // KPIs
  let totalRec=0,totalDesp=0;
  lista.forEach(it=>{
    if(it.tipo==='RECEITA') totalRec += Number(it.valor);
    if(it.tipo==='DESPESA') totalDesp += Number(it.valor);
  });
  document.getElementById('kpiRec').textContent   = money(totalRec);
  document.getElementById('kpiDesp').textContent  = money(totalDesp);
  document.getElementById('kpiSaldo').textContent = money(totalRec-totalDesp);

  // tabela
  const tb = document.getElementById("lista");
  tb.innerHTML = '';
  lista.forEach(row=>{
    const tr = document.createElement('tr');

    const tdId = document.createElement('td'); tdId.textContent=row.id; tr.appendChild(tdId);
    const tdData = document.createElement('td'); tdData.textContent=row.data_mov; tr.appendChild(tdData);

    const tdTipo = document.createElement('td');
    const b = document.createElement('span'); b.className='badge '+(row.tipo==='RECEITA'?'income':'expense');
    b.textContent = row.tipo; tdTipo.appendChild(b); tr.appendChild(tdTipo);

    const tdCat = document.createElement('td'); tdCat.textContent=row.categoria; tr.appendChild(tdCat);
    const tdDesc= document.createElement('td'); tdDesc.textContent=row.descricao||''; tr.appendChild(tdDesc);
    const tdVal = document.createElement('td'); tdVal.textContent=money(row.valor); tr.appendChild(tdVal);

    const tdAcs = document.createElement('td');
    tdAcs.innerHTML = `
      <a class="btn" style="padding:6px 10px" href="../lancamento/alterar_lancamento.htm?id=${row.id}">Alterar</a>
      <button class="btn danger" style="padding:6px 10px" onclick="excluir(${row.id})">Excluir</button>
    `;
    tr.appendChild(tdAcs);

    tb.appendChild(tr);
  });

  document.getElementById('infoPagina').textContent = `Página ${j.pagina || pagina}`;
}

async function excluir(id){
  if(!confirm('Confirmar exclusão?')) return;
  const fd = new FormData(); fd.append("id", id);
  await fetch('../php/excluir_lancamento.php', { method:'POST', body: fd });
  lista();
}
