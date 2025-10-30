const $ = sel => document.querySelector(sel);

async function carregarMinhasFamilias() {
  const sel = $("#familia");
  if (!sel) return;
  sel.innerHTML = `<option value="">(carregando...)</option>`;

  try {
    const resp = await apiFetch('/familias/get.php');
    const txt  = await resp.text();
    let data;
    try {
      data = JSON.parse(txt);
    } catch (e) {
      console.error('GET familias (texto):', txt);
      sel.innerHTML = `<option value="">(erro ao carregar)</option>`;
      alert('Erro ao carregar famílias');
      return;
    }

    if (!data.ok) {
      sel.innerHTML = `<option value="">(erro ao carregar)</option>`;
      alert(data.msg || 'Erro ao carregar famílias');
      return;
    }

    const lista = data.data || data.familias || [];
    if (!lista.length) {
      sel.innerHTML = `<option value="">(nenhuma família)</option>`;
      limparMembros();
      return;
    }

    sel.innerHTML = '';
    for (const f of lista) {
      const opt = document.createElement('option');
      opt.value = f.id_familia ?? f.id ?? '';
      opt.textContent = f.nome ?? '(sem nome)';
      sel.appendChild(opt);
    }

    carregarMembros(sel.value);
  } catch (err) {
    console.error('ERRO carregarMinhasFamilias:', err);
    sel.innerHTML = `<option value="">(erro ao carregar)</option>`;
    alert('Falha de rede ao buscar famílias');
  }
}

function limparMembros() {
  const tb = $("#membros");
  if (tb) tb.innerHTML = `<tr><td colspan="3">Selecione uma família</td></tr>`;
}

async function carregarMembros(id_familia) {
  const tb = $("#membros");
  if (!tb) return;

  if (!id_familia) {
    limparMembros();
    return;
  }

  tb.innerHTML = `<tr><td colspan="3">Carregando...</td></tr>`;

  try {
    const resp = await apiFetch('/familias/membros.php?id_familia=' + encodeURIComponent(id_familia));
    const txt  = await resp.text();
    let data;
    try {
      data = JSON.parse(txt);
    } catch (e) {
      console.error('GET membros (texto):', txt);
      tb.innerHTML = `<tr><td colspan="3">Erro de rede ao listar membros</td></tr>`;
      return;
    }

    if (!data.ok) {
      tb.innerHTML = `<tr><td colspan="3">${data.msg || 'Erro ao carregar membros'}</td></tr>`;
      return;
    }

    const lista = data.data || [];
    if (!lista.length) {
      tb.innerHTML = `<tr><td colspan="3">Sem membros nessa família</td></tr>`;
      return;
    }

    tb.innerHTML = '';
    for (const m of lista) {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${m.nome  || '-'}</td>
        <td>${m.email || '-'}</td>
        <td>${m.papel || 'MEMBRO'}</td>
      `;
      tb.appendChild(tr);
    }
  } catch (err) {
    console.error('ERRO carregarMembros:', err);
    tb.innerHTML = `<tr><td colspan="3">Erro de rede ao listar membros</td></tr>`;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  carregarMinhasFamilias();

  const sel = $("#familia");
  if (sel) {
    sel.addEventListener('change', () => {
      carregarMembros(sel.value);
    });
  }

  const btnCriar = $("#btnCriarFam");
  if (btnCriar) {
    btnCriar.addEventListener('click', async () => {
      const nome = prompt('Nome da família:', 'Minha família');
      if (!nome) return;

      const resp = await apiFetch('/familias/create_familia.php', {
        method: 'POST',
        body: new URLSearchParams({ nome })
      });
      const txt = await resp.text();
      let data;
      try {
        data = JSON.parse(txt);
      } catch (e) {
        console.error('CREATE familia (texto):', txt);
        alert('Erro ao criar família');
        return;
      }

      if (!data.ok) {
        alert(data.msg || 'Erro ao criar família');
        return;
      }

      // recarrega lista
      await carregarMinhasFamilias();

      // se o back devolveu o id, já seleciona e carrega membros
      if (data.id_familia) {
        const s = $("#familia");
        if (s) {
          s.value = data.id_familia;
          carregarMembros(data.id_familia);
        }
      }

      alert('Família criada');
    });
  }

  const btnPerm = $("#btnPermissoes");
  if (btnPerm) {
    btnPerm.addEventListener('click', async () => {
      const s = $("#familia");
      const id_familia = s ? s.value : '';
      if (!id_familia) {
        alert('Selecione uma família primeiro');
        return;
      }

      const resp = await apiFetch('/familias/permissoes.php?id_familia=' + encodeURIComponent(id_familia));
      const txt  = await resp.text();
      let data;
      try {
        data = JSON.parse(txt);
      } catch (e) {
        console.error('PERMISSOES (texto):', txt);
        alert('Erro ao carregar permissões');
        return;
      }
      console.log('permissoes.php ->', data);
      alert(data.ok ? 'Veja o console (F12) para as permissões.' : (data.msg || 'Erro ao carregar permissões'));
    });
  }

  const formAdd = $("#formAdd");
  if (formAdd) {
    formAdd.addEventListener('submit', async (e) => {
      e.preventDefault();

      const s = $("#familia");
      const id_familia = s ? s.value : '';
      if (!id_familia) {
        alert('Selecione uma família primeiro');
        return;
      }

      const email = $("#novoEmail").value.trim();
      const papel = $("#novoPapel").value || 'MEMBRO';

      if (!email) {
        alert('Informe o e-mail do usuário');
        return;
      }

      const resp = await apiFetch('/familias/add_membro.php', {
        method: 'POST',
        body: new URLSearchParams({ id_familia, email, papel })
      });
      const txt  = await resp.text();
      let data;
      try {
        data = JSON.parse(txt);
      } catch (e) {
        console.error('ADD membro (texto):', txt);
        alert('Erro ao adicionar membro');
        return;
      }

      if (!data.ok) {
        alert(data.msg || 'Erro ao adicionar membro');
        return;
      }

      $("#novoEmail").value = '';
      await carregarMembros(id_familia);
    });
  }
});
