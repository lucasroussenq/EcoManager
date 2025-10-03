const $ = s => document.querySelector(s);

const params = new URLSearchParams(location.search);
const id = params.get('id');

if (!id) {
  alert('ID não informado');
  location.href = './index.html';
}

const $form    = $('#formAlt');
const $msg     = $('#msg');            // <p id="msg"> opcional para erros
const $catSel  = $('#id_categoria');
const $famSel  = $('#id_familia');     // se existir no HTML
const $data    = $('#data_mov');
const $desc    = $('#descricao');
const $tipo    = $('#tipo');
const $valor   = $('#valor');

function showMsg(text) {
  if ($msg) {
    $msg.textContent = text || '';
    $msg.style.display = text ? 'block' : 'none';
  } else if (text) {
    alert(text);
  }
}

async function carregaCategorias() {
  const r = await fetch('../php/get_categorias.php', { credentials: 'include' });
  const j = await r.json();
  if (!j.ok) throw new Error(j.msg || 'Erro ao carregar categorias');

  $catSel.innerHTML = '<option value="">Selecione...</option>';
  j.categorias.forEach(c => {
    const opt = document.createElement('option');
    opt.value = c.id_categoria;        // value numérico (ID)
    opt.textContent = c.nome;
    $catSel.appendChild(opt);
  });
}

async function carregaFamilias() {
  if (!$famSel) return;
  try {
    const r = await fetch('../php/get_familias.php', { credentials: 'include' });
    const j = await r.json();
    $famSel.innerHTML = '<option value="">Nenhuma</option>';
    if (j.ok && Array.isArray(j.familias)) {
      j.familias.forEach(f => {
        const opt = document.createElement('option');
        opt.value = f.id_familia;
        opt.textContent = f.nome;
        $famSel.appendChild(opt);
      });
    }
  } catch (_) { /* silencioso */ }
}

async function carregaLancamento() {
  const r = await fetch(`../php/get_lancamento.php?id=${encodeURIComponent(id)}`, { credentials: 'include' });
  const j = await r.json();
  if (!j.ok) throw new Error(j.msg || 'Falha ao carregar lançamento');

  $data.value  = j.item.data_mov;              // yyyy-mm-dd
  $desc.value  = j.item.descricao;
  $tipo.value  = j.item.tipo;
  $valor.value = j.item.valor;

  // precisa existir a option com esse ID (por isso carregaCategorias antes)
  $catSel.value = j.item.id_categoria;

  if ($famSel) $famSel.value = j.item.id_familia ?? '';
}

$form?.addEventListener('submit', async (e) => {
  e.preventDefault();
  showMsg('');

  if (!$catSel.value) {
    showMsg('Categoria obrigatória');
    return;
  }

  const fd = new FormData($form);
  fd.set('id', id); // garante que o ID vai no POST

  try {
    const r = await fetch('../php/alterar_lancamento.php', { method: 'POST', body: fd, credentials: 'include' });
    const j = await r.json();
    if (!j.ok) throw new Error(j.msg || 'Erro ao salvar alterações');
    location.href = './index.html';
  } catch (err) {
    showMsg(err.message);
  }
});

// inicialização
(async function init() {
  try {
    await carregaCategorias();
    await carregaFamilias();
    await carregaLancamento();
  } catch (e) {
    showMsg(e.message);
  }
})();
