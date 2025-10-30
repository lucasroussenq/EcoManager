// /public/assets/js/inserir_lancamento.js

// pega elementos
const $form = document.getElementById('formLanc');
const $msg  = document.getElementById('msg');
const $tipo = document.getElementById('tipo');
const $cat  = document.getElementById('id_categoria');
const $fam  = document.getElementById('id_familia');

/* =============================
   Carregar categorias por tipo
   ============================= */
async function carregarCategoriasPorTipo(tipo) {
  try {
    // tipo = 'DESPESA' ou 'RECEITA'
    const resp = await apiFetch(`/categorias/list.php?tipo=${encodeURIComponent(tipo)}`);
    const txt  = await resp.text();

    let j;
    try {
      j = JSON.parse(txt);
    } catch (e) {
      console.error('categorias/list.php retornou HTML:', txt);
      return;
    }

    $cat.innerHTML = '<option value="">Selecione...</option>';
    if (j.ok && Array.isArray(j.categorias)) {
      j.categorias.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = c.nome;
        $cat.appendChild(opt);
      });
    }
  } catch (err) {
    console.error('Erro ao carregar categorias:', err);
  }
}

/* =============================
   Carregar famílias do usuário
   ============================= */
async function carregarFamilias() {
  if (!$fam) return;

  $fam.innerHTML = '<option value="">Sem família</option>';

  try {
    const resp = await apiFetch('/familias/get.php');
    const txt  = await resp.text();

    let j;
    try {
      j = JSON.parse(txt);
    } catch (e) {
      console.error('familias/get.php retornou HTML:', txt);
      return;
    }

    if (!j.ok) {
      console.warn('familias/get.php ->', j.msg);
      return;
    }

    const lista = j.data || j.familias || [];
    lista.forEach(f => {
      const opt = document.createElement('option');
      opt.value = f.id_familia ?? f.id ?? '';
      opt.textContent = f.nome ?? '(sem nome)';
      $fam.appendChild(opt);
    });
  } catch (err) {
    console.error('Erro ao carregar famílias:', err);
  }
}

/* =============================
   Envio do formulário
   ============================= */
if ($form) {
  $form.addEventListener('submit', async (ev) => {
    ev.preventDefault();
    if ($msg) {
      $msg.style.display = 'none';
      $msg.textContent = '';
    }

    const fd = new FormData($form);

    // se não escolher família, manda string vazia
    if (!fd.get('id_familia')) {
      fd.set('id_familia', '');
    }

    try {
      // AQUI: volta para o endpoint que EXISTE
      const resp = await apiFetch('/lancamento/create.php', {
        method: 'POST',
        body: fd
      });

      const txt = await resp.text();
      let j;
      try {
        j = JSON.parse(txt);
      } catch (e) {
        console.error('Resposta NÃO-JSON do create.php:', txt);
        throw new Error('Falha na comunicação com o servidor.');
      }

      if (!j.ok) {
        throw new Error(j.msg || 'Erro ao salvar lançamento.');
      }

      alert('Lançamento salvo com sucesso!');

      // redireciona para uma página que exista
      window.location.href = '/ecomanager/public/lancamento/index.html';
    } catch (err) {
      console.error(err);
      if ($msg) {
        $msg.textContent = err.message;
        $msg.style.display = 'block';
      } else {
        alert(err.message);
      }
    }
  });
}

/* =============================
   Inicialização
   ============================= */
document.addEventListener('DOMContentLoaded', () => {
  // quando muda o tipo, recarrega categorias
  if ($tipo) {
    $tipo.addEventListener('change', () => {
      carregarCategoriasPorTipo($tipo.value);
    });

    // carrega uma vez no começo
    carregarCategoriasPorTipo($tipo.value);
  }

  // carrega famílias
  carregarFamilias();
});
