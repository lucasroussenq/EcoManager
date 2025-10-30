const $ = s => document.querySelector(s);
const params = new URLSearchParams(location.search);
const id = params.get('id');
if (!id) { alert('ID não informado'); location.href = './index.html'; }

const $form = $('#formAlt'), $msg = $('#msg'), $catSel = $('#id_categoria'),
      $famSel = $('#id_familia'), $data = $('#data_mov'),
      $desc = $('#descricao'), $tipo = $('#tipo'), $valor = $('#valor');

const showMsg = t => { if ($msg) { $msg.textContent = t || ''; $msg.style.display = t ? 'block' : 'none'; } };

async function carregaCategorias(){
  const r = await apiFetch('/categorias/list.php');
  const j = await r.json().catch(async () => { console.error(await r.text()); throw new Error('Erro ao carregar categorias'); });
  if(!j.ok) throw new Error(j.msg||'Erro ao carregar categorias');
  $catSel.innerHTML = '<option value="">Selecione...</option>';
  (j.categorias||[]).forEach(c=>{
    const o = document.createElement('option');
    o.value = c.id;
    o.textContent = c.nome;
    $catSel.appendChild(o);
  });
}

async function carregaFamilias(){
  if (!$famSel) return;
  try {
    const r = await apiFetch('/familias/get.php');
    const j = await r.json();
    $famSel.innerHTML = '<option value="">Nenhuma</option>';
    if (j.ok && Array.isArray(j.data)) {
      j.data.forEach(f => {
        const o = document.createElement('option');
        o.value = f.id_familia;
        o.textContent = f.nome;
        $famSel.appendChild(o);
      });
    }
  } catch (_) {}
}

async function carregaLancamento(){
  const r = await apiFetch(`/lancamento/item.php?id=${encodeURIComponent(id)}`);
  const j = await r.json().catch(async () => { console.error(await r.text()); throw new Error('Falha ao carregar lançamento'); });
  if(!j.ok) throw new Error(j.msg||'Falha ao carregar lançamento');
  $data.value  = j.item.data_mov;
  $desc.value  = j.item.descricao;
  $tipo.value  = j.item.tipo;
  $valor.value = j.item.valor;
  $catSel.value = j.item.id_categoria;
  if ($famSel) $famSel.value = j.item.id_familia ?? '';
}

$form?.addEventListener('submit', async (e)=>{
  e.preventDefault(); showMsg('');
  if(!$catSel.value){ showMsg('Categoria obrigatória'); return; }
  const fd = new FormData($form); fd.set('id', id);
  try{
    const r = await apiFetch('/lancamento/update.php', { method:'POST', body: fd });
    const j = await r.json().catch(async () => { console.error(await r.text()); throw new Error('Erro ao salvar alterações'); });
    if(!j.ok) throw new Error(j.msg||'Erro ao salvar alterações');
    location.href = './index.html';
  }catch(err){ showMsg(err.message); }
});

(async ()=>{ try { await carregaCategorias(); await carregaFamilias(); await carregaLancamento(); } catch(e){ showMsg(e.message); } })();
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formAlterarLancamento');
  if (!form) return;

  const msg = document.getElementById('msgErro'); // opcional

  form.addEventListener('submit', async (ev) => {
    ev.preventDefault();

    const fd = new FormData(form);

    try {
      const resp = await fetch('/ecomanager/api/lancamento/atualizar.php', {
        method: 'POST',
        body: fd,
        credentials: 'include'
      });

      // LER UMA ÚNICA VEZ
      const text = await resp.text();

      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        console.error('Resposta não-JSON do atualizar.php:', text);
        if (msg) msg.textContent = text || 'Erro ao atualizar.';
        alert(text || 'Erro ao atualizar.');
        return;
      }

      if (!resp.ok || data.ok === false) {
        const m = data.msg || 'Erro ao atualizar lançamento.';
        if (msg) msg.textContent = m;
        alert(m);
        return;
      }

      if (msg) msg.textContent = '';
      alert(data.msg || 'Lançamento atualizado com sucesso!');
      // volta pra lista
      window.location.href = '/ecomanager/public/lancamento/listar.html';
    } catch (err) {
      console.error(err);
      if (msg) msg.textContent = 'Falha na comunicação com o servidor.';
      alert('Falha na comunicação com o servidor.');
    }
  });
});
