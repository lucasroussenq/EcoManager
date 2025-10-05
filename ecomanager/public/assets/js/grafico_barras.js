const cv = document.getElementById('barras');
const cx = cv.getContext('2d');
const mesInp = document.getElementById('mes');
document.getElementById('btnAtualizar').addEventListener('click', desenhar);

async function fetchData(){
  const r = await apiFetch(`/graficos/mensal.php?mes=${encodeURIComponent(mesInp.value||'')}`);
  const j = await r.json();
  if (!j.ok) throw new Error(j.msg||'Erro dados');
  return j; 
}
function drawBars(rec, desp){
  cx.clearRect(0,0,700,380);
  const baseX = 120, baseY = 320, w = 180, gap=140, max = Math.max(rec,desp,1);
  cx.strokeStyle = '#94a3b8'; cx.beginPath(); cx.moveTo(60,baseY); cx.lineTo(660,baseY); cx.stroke();
  const hR = (rec/max)*250, hD = (desp/max)*250;
  cx.fillStyle = 'hsl(160 70% 50%)'; cx.fillRect(baseX, baseY - hR, w, hR);
  cx.fillStyle='#dbe7ff'; cx.font='14px sans-serif';
  cx.fillText(`Receitas: R$ ${rec.toFixed(2)}`, baseX-20, baseY - hR - 8);
  cx.fillStyle = 'hsl(12 80% 55%)'; cx.fillRect(baseX + w + gap, baseY - hD, w, hD);
  cx.fillStyle='#dbe7ff';
  cx.fillText(`Despesas: R$ ${desp.toFixed(2)}`, baseX + w + gap - 20, baseY - hD - 8);
}
async function desenhar(){
  try { const d = await fetchData(); drawBars(Number(d.receita||0), Number(d.despesa||0)); }
  catch(e){ alert(e.message); }
}
desenhar();
