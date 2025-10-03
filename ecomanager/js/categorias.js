const $ = s => document.querySelector(s);

async function carregar() {
  const r = await fetch('../php/categoria_list.php');
  const j = await r.json();
  if (!j.ok) { alert(j.msg || 'Erro ao listar'); return; }
  const tb = $('#lista'); tb.innerHTML = '';
  j.itens.forEach((c) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${c.id_categoria}</td>
      <td>${c.nome}</td>
      <td>${c.natureza}</td>
      <td>
        <a href="#" data-editar="${c.id_categoria}">Editar</a> •
        <a href="#" data-excluir="${c.id_categoria}">Excluir</a>
      </td>`;
    tb.appendChild(tr);
  });
}
carregar();

$('#lista').addEventListener('click', async (e) => {
  const a = e.target.closest('a');
  if (!a) return;
  e.preventDefault();

  if (a.dataset.editar) {
    const id = a.dataset.editar;
    const r = await fetch(`../php/categoria_list.php?id=${id}`);
    const j = await r.json();
    if (!j.ok) { alert(j.msg); return; }
    const c = j.itens[0];
    $('#id_categoria').value = c.id_categoria;
    $('#nome').value = c.nome;
    $('#id_natureza').value = c.id_natureza;
  }

  if (a.dataset.excluir) {
    if (!confirm('Excluir categoria?')) return;
    const fd = new FormData();
    fd.append('id', a.dataset.excluir);
    const r = await fetch('../php/categoria_excluir.php', { method:'POST', body:fd });
    const j = await r.json();
    if (!j.ok) alert(j.msg || 'Erro'); else carregar();
  }
});

$('#formCat').addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const r = await fetch('../php/categoria_salvar.php', { method:'POST', body:fd });
  const j = await r.json();
  if (!j.ok) alert(j.msg || 'Erro'); else { e.target.reset(); carregar(); }
});
$('#btnNovo').addEventListener('click', () => { $('#formCat').reset(); $('#id_categoria').value=''; });
