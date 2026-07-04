{{-- Компонент: модальное окно просмотра счёта --}}
{{-- Использование: <x-cabinet.invoice-view-modal /> --}}
{{-- JS API: window.openInvoiceModal(id) --}}
{{-- Зависимости страницы: CSRF, escHtml(), fmtMoney() должны быть объявлены в скриптах страницы --}}
{{-- CSS: resources/css/components/cabinet/doc-view-modal.scss --}}

<div class="modal-overlay" id="invoice-view-modal">
  <div class="modal" style="max-width:620px;padding:0;">
    <div class="modal-head">
      <div>
        <div class="modal-title" id="ivm-title">Счёт</div>
        <div class="modal-sub" id="ivm-basis"></div>
      </div>
      <button class="modal-x" data-close="invoice-view-modal">×</button>
    </div>
    <div id="ivm-body"></div>
    <div class="modal-foot">
      <div style="padding: 22px 0 0 0">
        <a id="ivm-pdf-link" href="#" target="_blank" class="btn btn-primary btn-sm">
          <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 1.5v6M3.5 5.5 6 8l2.5-2.5M1.5 10.5h9"/></svg>
          Скачать PDF
        </a>
      </div>
    </div>
  </div>
</div>

@once
@push('scripts')
<script>
(function () {
  const modal   = document.getElementById('invoice-view-modal');
  const titleEl = document.getElementById('ivm-title');
  const basisEl = document.getElementById('ivm-basis');
  const bodyEl  = document.getElementById('ivm-body');

  function closeModal() { modal.classList.remove('open'); }

  window.openInvoiceModal = async function (id) {
    modal.classList.add('open');
    titleEl.textContent = 'Загрузка…';
    basisEl.textContent = '';
    bodyEl.innerHTML = '';
    try {
      const res = await fetch('/cabinet/invoices/' + id, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      });
      if (!res.ok) throw new Error();
      const inv = await res.json();
      const p = (inv.date || '').split('-');
      const dateStr = p[2] ? `${p[2]}.${p[1]}.${p[0]}` : '—';
      titleEl.textContent = `Счёт № ${inv.number} от ${dateStr}`;
      basisEl.textContent = inv.contract_name || inv.basis || '';
      document.getElementById('ivm-pdf-link').href = '/cabinet/invoices/' + id + '/pdf';
      const itemRows = inv.items.map(it => `
        <tr>
          <td>${escHtml(it.name)}</td>
          <td style="white-space:nowrap;">${escHtml(it.unit)}</td>
          <td style="text-align:center;">${it.quantity % 1 === 0 ? it.quantity : it.quantity.toFixed(2)}</td>
          <td style="text-align:right;white-space:nowrap;">${fmtMoney(it.price)}</td>
          <td style="text-align:right;white-space:nowrap;">${fmtMoney(it.amount)}</td>
        </tr>`).join('');
      bodyEl.innerHTML = `
        <div class="ivm-table-wrap">
          <table class="ivm-table">
            <thead><tr><th>Наименование</th><th>Ед.</th><th style="text-align:center;">Кол.</th><th style="text-align:right;">Цена</th><th style="text-align:right;">Сумма</th></tr></thead>
            <tbody>${itemRows}</tbody>
          </table>
        </div>
        <div class="ivm-totals">
          <div class="ivm-total-row"><span>Итого без НДС:</span><span>${fmtMoney(inv.subtotal)}</span></div>
          ${inv.nds_amount > 0 ? `<div class="ivm-total-row"><span>НДС:</span><span>${fmtMoney(inv.nds_amount)}</span></div>` : ''}
          <div class="ivm-total-row ivm-total-final"><span>Всего к оплате:</span><span>${fmtMoney(inv.total)}</span></div>
        </div>`;
    } catch {
      bodyEl.innerHTML = '<div style="padding:24px;text-align:center;font-size:13px;color:var(--text-s);">Не удалось загрузить данные счёта</div>';
    }
  };

  modal.querySelectorAll('[data-close]').forEach(el => el.addEventListener('click', closeModal));
  modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
})();
</script>
@endpush
@endonce
