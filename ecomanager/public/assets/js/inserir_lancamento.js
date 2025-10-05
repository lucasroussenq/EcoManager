const $cat = document.getElementById('id_categoria');
const $msg = document.getElementById('msg');

async function carregarCategorias() {
  try {
    const r = await apiFetch('/categorias/list.php');
    const j = await r.json();         
    if (!j.ok) throw new Error(j.msg || 'Erro ao listar categorias');
    if (!j.categorias || j.categorias.length === 0) {
      throw new Error('Nenhuma categoria cadastrada');
    }
    $cat.innerHTML = '<option value="">Selecione...</option>';
    j.categorias.forEach(c => {
      const opt = document.createElement('option');
      opt.value = c.id;
      opt.textContent = c.nome;
      $cat.appendChild(opt);
    });
    if ($msg) $msg.style.display = 'none';
  } catch (e) {
    if ($msg) {
      $msg.textContent = e.message;
      $msg.style.display = 'block';
    }
    console.error('get_categorias erro:', e);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const dataInput = document.getElementById('data_mov');
  if (dataInput && !dataInput.value) {
    const d = new Date();
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth()+1).padStart(2,'0');
    const dd = String(d.getDate()).padStart(2,'0');
    dataInput.value = `${yyyy}-${mm}-${dd}`;
  }
  carregarCategorias();
});

document.getElementById('formLanc').addEventListener('submit', async (e) => {
  e.preventDefault();
  if ($msg) $msg.style.display = 'none';

  try {
    const r = await apiFetch('/lancamento/create.php', {
      method: 'POST',
      body: new FormData(e.target)
    });

    let j;
    try { j = await r.json(); }
    catch (e2) {
      const raw = await r.text();
      console.error('Resposta create (texto):', raw);
      throw new Error('Erro ao salvar');
    }

    if (!j.ok) throw new Error(j.msg || 'Erro ao salvar');
    location.href = './index.html';
  } catch (err) {
    if ($msg) { $msg.textContent = err.message; $msg.style.display = 'block'; }
  }
});
