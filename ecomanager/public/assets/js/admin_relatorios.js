document.addEventListener('DOMContentLoaded', () => {
  const box = document.getElementById('boxRelatorio');
  const btnPdf = document.getElementById('btnPdf');

  async function carregar() {
    try {
      // ENDEREÇO CERTO AQUI 👇
      const r = await fetch('/ecomanager/api/relatorios/visao_geral.php', {
        credentials: 'include',
        cache: 'no-store'
      });
      const j = await r.json();

      if (!j.ok) {
        box.textContent = j.msg || 'Erro ao carregar relatório.';
        return;
      }

      box.innerHTML = `
        <p><strong>Total de usuários:</strong> ${j.tot_usuarios}</p>
        <p><strong>Total de famílias:</strong> ${j.tot_familias}</p>
        <p><strong>Lançamentos do mês:</strong> ${j.lancamentos_mes}</p>
        <p><strong>Receitas (mês):</strong> R$ ${Number(j.receitas_mes).toFixed(2)}</p>
        <p><strong>Despesas (mês):</strong> R$ ${Number(j.despesas_mes).toFixed(2)}</p>
        <p><strong>Saldo (mês):</strong> R$ ${Number(j.saldo_mes).toFixed(2)}</p>
      `;
    } catch (e) {
      console.error(e);
      box.textContent = 'Erro ao carregar relatório.';
    }
  }

  carregar();

  // por enquanto o PDF só mostra alerta
  if (btnPdf) {
   btnPdf.addEventListener('click', () => {
  window.open('/ecomanager/api/relatorios/exportar_pdf.php', '_blank');
    });
  }
});
