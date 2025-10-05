// /public/assets/js/grafico_pizza.js
const ctx = document.getElementById('pizza')?.getContext('2d');
const mesInp = document.getElementById('mes');
document.getElementById('btnAtualizar').addEventListener('click', desenhar);

async function fetchData() {
  const r = await apiFetch('/graficos/categoria.php?mes=' + encodeURIComponent(mesInp.value));
  const j = await r.json();
  if (!j.ok) throw new Error(j.msg || 'Erro dados');
  return j;
}

function drawPie(labels, values) {
  if (!ctx) return;
  ctx.clearRect(0, 0, 600, 380);
  const total = values.reduce((a, b) => a + b, 0) || 1;
  let start = -Math.PI / 2;

  values.forEach((v, i) => {
    const ang = (v / total) * 2 * Math.PI;
    const hue = (i * 67) % 360;
    ctx.beginPath();
    ctx.moveTo(300, 190);
    ctx.fillStyle = `hsl(${hue}, 70%, 50%)`;
    ctx.arc(300, 190, 150, start, start + ang);
    ctx.closePath();
    ctx.fill();

    // label
    ctx.fillStyle = '#dbe7ff';
    ctx.font = '14px sans-serif';
    ctx.fillText(`${labels[i]} (R$ ${v.toFixed(2)})`, 10, 20 + 18 * i);
    start += ang;
  });
}

async function desenhar() {
  try {
    const d = await fetchData();
    drawPie(d.labels || [], d.valores.map(Number) || []);
  } catch (e) {
    alert(e.message);
  }
}
desenhar();