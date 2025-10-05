const $ = s => document.querySelector(s);

async function carregar() {
  const r = await apiFetch('/categorias/list.php');
  const j = await r.json().catch(async ()=>{ console.error('resp:', await r.text()); return {ok:false,msg:'Erro ao listar'}; });
  if (!j.ok) { alert(j.msg || 'Erro ao listar'); return; }

  const tb = $('#lista'); tb.innerHTML = '';
  (j.categorias||[]).forEach((c) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${c.id}</td>
      <td>${c.nome}</td>
      <td>${c.natureza ?? '-'}</td>
      <td>
        <a href="#" data-editar="${c.id}">Editar</a> •
        <a href="#" data-excluir="${c.id}">Excluir</a>
      </td>`;
    tb.appendChild(tr);
  });
}
carregar();

$('#lista').addEventListener('click', async (e) => {
  const a = e.target.closest('a'); if (!a) return;
  e.preventDefault();

  if (a.dataset.editar) {
    const id = a.dataset.editar;
    const r = await apiFetch(`/categorias/list.php?id=${id}`);
    const j = await r.json().catch(async ()=>{ console.error(await r.text()); return {ok:false}; });
    if (!j.ok) { alert(j.msg || 'Erro'); return; }
    const c = j.categorias[0];
    $('#id_categoria').value = c.id;
    $('#nome').value = c.nome;
    if (c.id_natureza) $('#id_natureza').value = c.id_natureza;
  }

  if (a.dataset.excluir) {
    if (!confirm('Excluir categoria?')) return;
    const r = await apiFetch('/categorias/delete.php', {
      method:'POST',
      body: new URLSearchParams({ id: a.dataset.excluir })
    });
    const j = await r.json().catch(async ()=>({ok:false,msg:await r.text()}));
    if (!j.ok) alert(j.msg || 'Erro'); else carregar();
  }
});

$('#formCat').addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const r = await apiFetch('/categorias/save.php', { method:'POST', body:fd });
  const j = await r.json().catch(async ()=>({ok:false,msg:await r.text()}));
  if (!j.ok) alert(j.msg || 'Erro'); else { e.target.reset(); carregar(); }
});
$('#btnNovo').addEventListener('click', () => { $('#formCat').reset(); $('#id_categoria').value=''; });
