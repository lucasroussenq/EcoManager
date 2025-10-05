document.addEventListener("DOMContentLoaded", () => {
  const b = document.getElementById("btnRecorrentes");
  if (!b) return;

  b.addEventListener("click", async ()=>{
    const mes = document.getElementById("mes").value.trim(); // YYYYMM
    if (!mes) { alert("Informe o MÊS (YYYYMM)."); return; }

    const r = await apiFetch('/lancamento/recorrente/create.php', {
      method:"POST",
      body: new URLSearchParams({ mes })
    });
    const j = await r.json().catch(async ()=>({ok:false,msg:await r.text()}));
    if (!j.ok) { alert(j.msg || "Erro ao gerar recorrentes."); return; }
    alert(j.msg);
    if (window.carregarLancamentos) carregarLancamentos();
  });
});
