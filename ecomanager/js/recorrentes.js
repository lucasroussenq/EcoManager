// js/recorrentes.js
document.addEventListener("DOMContentLoaded", () => {
  const b = document.getElementById("btnRecorrentes");
  if (!b) return;

  b.addEventListener("click", async ()=>{
    const mes = document.getElementById("mes").value.trim(); // YYYYMM
    if (!mes) { alert("Informe o MÊS (YYYYMM)."); return; }

    const r = await fetch("../php/recorrentes_gerar.php", {
      method:"POST",
      headers: {"Content-Type":"application/json"},
      body: JSON.stringify({ mes })
    });
    const j = await r.json();
    if (!j.ok) { alert(j.msg || "Erro ao gerar recorrentes."); return; }
    alert(j.msg);
    // recarrega a lista
    if (window.lista) lista();
  });
});
