// js/grafico_pizza.js
async function carregarGraficoPizza() {
  const mes = document.getElementById("mes").value.trim(); // YYYYMM (do seu filtro)
  const famSel = document.getElementById("familia") ? document.getElementById("familia").value : "";
  const tipoSel = document.getElementById("tipo") ? document.getElementById("tipo").value : "";

  const qs = new URLSearchParams({ mes });
  if (famSel) qs.set("id_familia", famSel);
  if (tipoSel && tipoSel !== "Todos") qs.set("tipo", tipoSel);

  const r = await fetch(`../php/get_grafico_categoria.php?${qs.toString()}`);
  const j = await r.json();
  if (!j.ok) { console.warn(j.msg); return; }

  const data = j.data || [];
  desenharPizza("pizzaCategorias", data);
}

function desenharPizza(canvasId, pares) {
  const cvs = document.getElementById(canvasId);
  if (!cvs) return;
  const ctx = cvs.getContext("2d");
  const W = cvs.width, H = cvs.height;
  ctx.clearRect(0,0,W,H);

  const total = pares.reduce((s,p)=> s + Number(p.total || 0), 0);
  if (total <= 0) {
    ctx.fillStyle = "#94a3b8";
    ctx.textAlign = "center";
    ctx.font = "14px Inter, Arial";
    ctx.fillText("Sem dados no período", W/2, H/2);
    return;
  }

  let ang = -Math.PI/2;        // começa no topo
  const cx = W/2, cy = H/2, r = Math.min(W,H)/2 - 10, r2 = r*0.6;

  pares.forEach((p,i)=>{
    const val = Number(p.total||0);
    const fra = val/total;
    const a2 = ang + fra * 2*Math.PI;

    // cor gerada dinamicamente
    const hue = Math.floor(360 * i/Math.max(1,pares.length));
    ctx.beginPath();
    ctx.moveTo(cx,cy);
    ctx.arc(cx,cy, r, ang, a2);
    ctx.closePath();
    ctx.fillStyle = `hsl(${hue} 80% 55% / .9)`;
    ctx.fill();

    // fatia interna (donut)
    ctx.globalCompositeOperation = "destination-out";
    ctx.beginPath();
    ctx.arc(cx,cy, r2, 0, 2*Math.PI);
    ctx.fill();
    ctx.globalCompositeOperation = "source-over";

    // label (categoria)
    const mid = (ang+a2)/2;
    const lx = cx + Math.cos(mid)*(r*0.8);
    const ly = cy + Math.sin(mid)*(r*0.8);
    ctx.fillStyle = "#dbe7ff";
    ctx.textAlign = "center";
    ctx.font = "12px Inter, Arial";
    ctx.fillText(p.categoria, lx, ly);

    ang = a2;
  });

  // total no centro
  ctx.fillStyle = "#dbe7ff";
  ctx.textAlign = "center";
  ctx.font = "bold 16px Inter, Arial";
  ctx.fillText("Total", cx, cy-4);
  ctx.font = "bold 18px Inter, Arial";
  ctx.fillText(`R$ ${total.toFixed(2)}`, cx, cy+18);
}

// opcional: auto carregar quando a página filtrar
document.addEventListener("DOMContentLoaded", ()=>{
  const btn = document.getElementById("btnGraficoPizza");
  if (btn) btn.addEventListener("click", carregarGraficoPizza);
});
