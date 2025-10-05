document.getElementById("cadastrar").addEventListener("click", async () => {
  const nome  = document.getElementById("nome").value.trim();
  const email = document.getElementById("email").value.trim();
  const senha = document.getElementById("senha").value;

  const resp = await apiFetch('/auth/registrar.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded;charset=UTF-8'},
    body: new URLSearchParams({ nome, email, senha })
  });
  const j = await resp.json().catch(async ()=>{ console.error(await resp.text()); return {ok:false,msg:'Falha no cadastro'}; });

  if (j.ok) {
    alert("Cadastro realizado com sucesso!");
    location.replace('/ecomanager/public/auth/login.html');
  } else {
    alert("Erro: " + (j.msg || 'Falha no cadastro'));
  }
});
