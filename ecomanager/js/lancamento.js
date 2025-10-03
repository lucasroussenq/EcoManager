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

// Função para formatar valor
function formatarValor(v){
  return `R$ ${v.toLocaleString('pt-BR', {minimumFractionDigits:2})}`;
}

// Carrega categorias no filtro
async function carregarCategorias(){
  const r = await fetch('../php/get_categorias.php', {credentials:'include'});
  const j = await r.json();
  if(j.ok){
    $f_categoria.innerHTML = '<option value="">Todas</option>';
    j.categorias.forEach(c=>{
      const o = document.createElement('option');
      o.value = c.id_categoria;
      o.textContent = c.nome;
      $f_categoria.appendChild(o);
    });
  }
}

// Carrega lançamentos
async function carregarLancamentos(){
  $msgLista.style.display='none';
  $lista.innerHTML = '';

  const params = new URLSearchParams();
  if($mes.value.trim()) params.set('mes',$mes.value.trim());
  if($tipo.value.trim()) params.set('tipo',$tipo.value.trim());
  if($f_categoria.value.trim()) params.set('id_categoria',$f_categoria.value.trim());

  try{
    const r = await fetch(`../php/get_lancamentos.php?${params.toString()}`, {credentials:'include'});
    const j = await r.json();
    if(!j.ok) throw new Error(j.msg || 'Erro ao carregar');

    let html='';
    j.registros.forEach((l,i)=>{
      html += `<tr>
        <td>${i+1}</td>
        <td>${l.data_br}</td>
        <td>${l.tipo}</td>
        <td>${l.categoria||''}</td>
        <td>${l.descricao}</td>
        <td>${formatarValor(l.valor)}</td>
        <td>
          <a href="./alterar_lancamento.html?id=${l.id}">Editar</a> |
          <a href="#" onclick="excluirLancamento(${l.id});return false;">Excluir</a>
        </td>
      </tr>`;
    });
    $lista.innerHTML = html || `<tr><td colspan="7">Nenhum lançamento encontrado.</td></tr>`;

    $kpiReceitas.textContent = formatarValor(j.totais.receitas);
    $kpiDespesas.textContent = formatarValor(j.totais.despesas);
    $kpiSaldo.textContent    = formatarValor(j.totais.saldo);

  }catch(e){
    console.error(e);
    $msgLista.textContent = e.message;
    $msgLista.style.display='block';
  }
}

// Excluir lançamento
async function excluirLancamento(id){
  if(!confirm('Excluir lançamento?')) return;
  const fd = new FormData();
  fd.set('id',id);
  const r = await fetch('../php/excluir_lancamento.php',{method:'POST',body:fd,credentials:'include'});
  const j = await r.json();
  if(j.ok) carregarLancamentos(); else alert(j.msg||'Erro ao excluir');
}

// Eventos
$btnFiltrar.addEventListener('click', carregarLancamentos);
$btnLimpar.addEventListener('click', ()=>{ $mes.value=''; $tipo.value=''; $f_categoria.value=''; carregarLancamentos(); });
$btnSair.addEventListener('click', async ()=>{ await fetch('../php/auth_logout.php',{credentials:'include'}); location.href='../auth/login.html'; });

// Inicializa
(async function(){
  await carregarCategorias();
  await carregarLancamentos();
})();
