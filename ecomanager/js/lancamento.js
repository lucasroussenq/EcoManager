const fmtBRL = v => v.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

const $mes = document.getElementById('mes');
const $tipo = document.getElementById('tipo');
const $cat  = document.getElementById('f_categoria');
const $btnFiltrar = document.getElementById('btnFiltrar');
const $btnLimpar  = document.getElementById('btnLimpar');
const $btnSair    = document.getElementById('btnSair');

const $kDespesas = document.getElementById('kpi_despesas');
const $kReceitas = document.getElementById('kpi_receitas');
const $kSaldo    = document.getElementById('kpi_saldo');

const $lista   = document.getElementById('lista');
const $msgLista = document.getElementById('msgLista');

// mês padrão = atual (YYYYMM)
(function setMesPadrao(){
  const d = new Date();
  const y = d.getFullYear().toString();
  const m = String(d.getMonth()+1).padStart(2,'0');
  $mes.value = `${y}${m}`;
})();

// carregar categorias (filtro)
async function carregarCategoriasFiltro() {
  try {
    const res = await fetch('../php/get_categorias.php');
    const data = await res.json();
    if (!data.ok) throw new Error(data.msg || 'Falha ao obter categorias');
    // mantém a opção "Todas"
    data.categorias.forEach(c => {
      const opt = document.createElement('option');
      opt.value = c.id_categoria;
      opt.textContent = c.nome;
      $cat.appendChild(opt);
    });
  } catch (e) {
    console.error(e);
  }
}

// listar lançamentos
async function listar() {
  $msgLista.style.display = 'none';
  $lista.innerHTML = '<tr><td colspan="7">Carregando...</td></tr>';
  try {
    const p = new URLSearchParams();
    if ($mes.value.trim()) p.set('mes', $mes.value.trim());
    if ($tipo.value)       p.set('tipo', $tipo.value);
    if ($cat.value)        p.set('id_categoria', $cat.value);

    const res = await fetch(`../php/get_lancamento.php?${p.toString()}`);
    const data = await res.json();

    if (!data.ok) throw new Error(data.msg || 'Erro ao listar');

    // KPIs
    const { receitas=0, despesas=0, saldo=0 } = data.totais || {};
    $kReceitas.textContent = fmtBRL(receitas);
    $kDespesas.textContent = fmtBRL(despesas);
    $kSaldo.textContent    = fmtBRL(saldo);

    // Tabela
    if (!data.registros || data.registros.length === 0) {
      $lista.innerHTML = '<tr><td colspan="7">Nenhum lançamento encontrado.</td></tr>';
      return;
    }

    $lista.innerHTML = data.registros.map((r, i) => `
      <tr>
        <td>${i+1}</td>
        <td>${r.data_br}</td>
        <td>${r.tipo}</td>
        <td>${r.categoria ?? '-'}</td>
        <td>${r.descricao ?? '-'}</td>
        <td>${fmtBRL(Number(r.valor||0))}</td>
        <td>
          <a href="#" data-id="${r.id}" class="acao-editar" style="margin-right:8px">Editar</a>
          <a href="#" data-id="${r.id}" class="acao-excluir">Excluir</a>
        </td>
      </tr>
    `).join('');
  } catch (e) {
    console.error(e);
    $lista.innerHTML = '';
    $msgLista.textContent = 'Erro ao carregar lançamentos.';
    $msgLista.style.display = 'block';
  }
}

$btnFiltrar.addEventListener('click', listar);
$btnLimpar.addEventListener('click', () => {
  $mes.value = '';
  $tipo.value = '';
  $cat.value = '';
  listar();
});

$btnSair?.addEventListener('click', async () => {
  try {
    const r = await fetch('../php/auth_logout.php', { method: 'POST' });
    const j = await r.json();
    // vai para a tela pública/raiz do módulo
    window.location.href = '../index.html';
  } catch (_) {
    window.location.href = '../index.html';
  }
});

// inicialização
carregarCategoriasFiltro().then(listar);
