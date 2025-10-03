const $cat = document.getElementById('id_categoria');
const $msg = document.getElementById('msg');

async function carregarCategorias() {
  try {
    const r = await fetch('../php/get_categorias.php', {
      method: 'GET',
      credentials: 'include',   // garante envio do cookie de sessão
      cache: 'no-store'
    });
    const j = await r.json();
    if (!j.ok) throw new Error(j.msg || 'Falha no carregamento');
    $cat.innerHTML = '<option value="">Selecione...</option>';
    j.categorias.forEach(c => {
      const opt = document.createElement('option');
      opt.value = c.id;
      opt.textContent = c.nome;
      $cat.appendChild(opt);
    });
    $msg.style.display = 'none';
  } catch (e) {
    $msg.textContent = 'Erro ao carregar categorias.';
    $msg.style.display = 'block';
  }
}

document.addEventListener('DOMContentLoaded', carregarCategorias);

// submit do formulário (mantém o que você já tinha)
document.getElementById('formLanc').addEventListener('submit', async (e) => {
  e.preventDefault();
  $msg.style.display = 'none';

  const body = new FormData(e.target);
  try {
    const r = await fetch('../php/inserir_lancamento.php', {
      method: 'POST',
      credentials: 'include',
      body
    });
    const j = await r.json();
    if (!j.ok) throw new Error(j.msg || 'Erro ao salvar');
    window.location.href = './index.html';
  } catch (err) {
    $msg.textContent = err.message;
    $msg.style.display = 'block';
  }
});
