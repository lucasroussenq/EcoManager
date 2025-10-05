const $ = sel => document.querySelector(sel);

const $lista = $('#lista');
const $msgLista = $('#msgLista');
const $kpiReceitas = $('#kpi_receitas');
const $kpiDespesas = $('#kpi_despesas');
const $kpiSaldo = $('#kpi_saldo');
const $mes = $('#mes');
const $tipo = $('#tipo');
const $f_categoria = $('#f_categoria');
const $btnFiltrar = $('#btnFiltrar');
const $btnLimpar = $('#btnLimpar');
const $btnSair = $('#btnSair');

const formatarValor = v => `R$ ${(Number(v)||0).toLocaleString('pt-BR', {minimumFractionDigits:2})}`;

async function carregarCategorias(){
  const r = await apiFetch('/categorias/list.php');
  const j = await r.json().catch(async ()=>({ok:false}));
  if(j.ok){
    $f_categoria.innerHTML = '<option value="">Todas</option>';
    (j.categorias||[]).forEach(c=>{
      const o = document.createElement('option');
      o.value = c.id;
      o.textContent = c.nome;
      $f_categoria.appendChild(o);
    });
  }
}

async function carregarLancamentos(){
  $msgLista.style.display='none';
  $lista.innerHTML = '';

  const params = new URLSearchParams();
  if($mes.value.trim()) params.set('mes',$mes.value.trim());
  if($tipo.value.trim()) params.set('tipo',$tipo.value.trim());
  if($f_categoria.value.trim()) params.set('id_categoria',$f_categoria.value.trim());

  try{
    const r = await apiFetch(`/lancamento/list.php?${params.toString()}`);
    const j = await r.json().catch(async ()=>{ throw new Error(await r.text()); });
    if(!j.ok) throw new Error(j.msg || 'Erro ao carregar');

    let html='';
    (j.registros||[]).forEach((l,i)=>{
      html += `<tr>
        <td>${i+1}</td>
        <td>${l.data_br}</td>
        <td>${l.tipo}</td>
        <td>${l.categoria||''}</td>
        <td>${l.descricao||''}</td>
        <td>${formatarValor(l.valor)}</td>
        <td>
          <a href="./alterar_lancamento.html?id=${l.id}">Editar</a> |
          <a href="#" data-excluir="${l.id}">Excluir</a>
        </td>
      </tr>`;
    });
    $lista.innerHTML = html || `<tr><td colspan="7">Nenhum lançamento encontrado.</td></tr>`;

    $kpiReceitas.textContent = formatarValor(j.totais?.receitas||0);
    $kpiDespesas.textContent = formatarValor(j.totais?.despesas||0);
    $kpiSaldo.textContent    = formatarValor(j.totais?.saldo||0);

  }catch(e){
    console.error(e);
    $msgLista.textContent = e.message;
    $msgLista.style.display='block';
  }
}

$lista.addEventListener('click', async (e)=>{
  const a = e.target.closest('a[data-excluir]');
  if(!a) return;
  e.preventDefault();
  const id = a.getAttribute('data-excluir');
  if(!confirm('Excluir lançamento?')) return;
  const r = await apiFetch('/lancamento/delete.php', {
    method:'POST',
    body: new URLSearchParams({ id })
  });
  const j = await r.json().catch(async ()=>({ok:false,msg:await r.text()}));
  if(j.ok) carregarLancamentos(); else alert(j.msg||'Erro ao excluir');
});

$btnFiltrar.addEventListener('click', carregarLancamentos);
$btnLimpar.addEventListener('click', ()=>{ $mes.value=''; $tipo.value=''; $f_categoria.value=''; carregarLancamentos(); });
$btnSair.addEventListener('click', async () => {
  try { await apiFetch('/auth/logout.php', { method: 'POST' }); }
  catch(_){}
  location.replace('/ecomanager/public/auth/login.html');
});

(async function(){ await carregarCategorias(); await carregarLancamentos(); })();
