document.getElementById('loginForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const email = document.getElementById('email').value.trim();
  const senha = document.getElementById('senha').value;
  const msg = document.getElementById('loginMsg');

  try {
    const resp = await fetch('/ecomanager/php/auth_login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
      body: new URLSearchParams({ email, senha }),
      credentials: 'same-origin'
    });
    const data = await resp.json();
    if (!data.ok) { msg.textContent = data.msg || 'Falha no login'; return; }

    // vai direto para a página de lançamentos
    window.location.replace('/ecomanager/lancamento/index.html');
  } catch (err) {
    msg.textContent = 'Erro de conexão.';
  }
});
