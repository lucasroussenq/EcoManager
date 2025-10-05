document.getElementById('loginForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const email = document.getElementById('email').value.trim();
  const senha = document.getElementById('senha').value;
  const msg = document.getElementById('loginMsg');

  try {
    const resp = await apiFetch('/auth/login.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'},
      body: new URLSearchParams({ email, senha })
    });
    const data = await resp.json().catch(async ()=>({ok:false,msg:await resp.text()}));
    if (!data.ok) { msg.textContent = data.msg || 'Falha no login'; return; }

    location.replace('/ecomanager/public/lancamento/index.html');
  } catch {
    msg.textContent = 'Erro de conexão.';
  }
});
