document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formCadastro');
  const msg  = document.getElementById('cadMsg');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    msg.textContent = '';

    const nome  = document.getElementById('nome').value.trim();
    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('senha').value.trim();

    if (!nome || !email || !senha) {
      msg.textContent = 'Preencha nome, e-mail e senha.';
      return;
    }

    try {
      const r = await apiFetch('/auth/registrar.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'},
        body: new URLSearchParams({ nome, email, senha })
      });
      const j = await r.json().catch(async ()=>({ok:false,msg:await r.text()}));

      if (j.ok) window.location.href = '/ecomanager/public/auth/login.html';
      else msg.textContent = j.msg || 'Erro ao cadastrar.';
    } catch (err) {
      msg.textContent = 'Falha de rede.';
      console.error(err);
    }
  });
});
