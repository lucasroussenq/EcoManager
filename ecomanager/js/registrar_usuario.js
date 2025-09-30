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
      const r = await fetch('../php/auth_registrar.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json; charset=UTF-8' },
        body: JSON.stringify({ nome, email, senha })
      });
      const j = await r.json();

      if (j.ok) {
        // cadastrado -> manda pro login
        window.location.href = 'login.html';
      } else {
        msg.textContent = j.msg || 'Erro ao cadastrar.';
      }
    } catch (err) {
      msg.textContent = 'Falha de rede.';
      console.error(err);
    }
  });
});
