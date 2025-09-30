document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('loginForm');
  const msg  = document.getElementById('loginMsg');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    msg.textContent = '';

    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('senha').value.trim();

    if (!email || !senha) {
      msg.textContent = 'Informe e-mail e senha.';
      return;
    }

    try {
      const r = await fetch('../php/auth_login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json; charset=UTF-8' },
        body: JSON.stringify({ email, senha })
      });
      const j = await r.json();

      if (j.ok) {
        window.location.href = '../lancamento/index.html';
      } else {
        msg.textContent = j.msg || 'Credenciais inválidas.';
      }
    } catch (err) {
      msg.textContent = 'Falha de rede.';
      console.error(err);
    }
  });
});
