document.addEventListener("DOMContentLoaded", () => {
  const $mes = document.getElementById('mes');
  const $btn = document.getElementById('btnVer');
  const $msg = document.getElementById('msg');

  // coloca mês atual por padrão
  if ($mes && !$mes.value) {
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    $mes.value = `${y}${m}`;
  }

  $btn.addEventListener('click', async () => {
    if ($msg) $msg.style.display = 'none';

    const mes = $mes.value.trim();
    if (!/^\d{6}$/.test(mes)) {
      $msg.textContent = 'Use o formato YYYYMM, exemplo: 202510';
      $msg.style.display = 'block';
      return;
    }

    try {
      const r = await apiFetch(`/relatorios/familia_mensal.php?mes=${encodeURIComponent(mes)}`);
      const j = await r.json();

      if (!j.ok) {
        $msg.textContent = j.msg || 'Não foi possível gerar o relatório.';
        $msg.style.display = 'block';
        return;
      }

      // preencher
      document.getElementById('rMes').textContent = j.mes;
      document.getElementById('rFam').textContent = j.id_familia
        ? `Família #${j.id_familia}`
        : '—';

      document.getElementById('rReceitas').textContent = 'R$ ' + Number(j.receitas).toFixed(2);
      document.getElementById('rDespesas').textContent = 'R$ ' + Number(j.despesas).toFixed(2);
      document.getElementById('rSaldo').textContent = 'R$ ' + Number(j.saldo).toFixed(2);

      document.getElementById('resultado').style.display = 'block';
    } catch (err) {
      console.error(err);
      $msg.textContent = 'Erro de rede ao buscar relatório.';
      $msg.style.display = 'block';
    }
  });
});
