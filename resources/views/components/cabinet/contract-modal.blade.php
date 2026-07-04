{{-- Компонент: модальное окно создания / редактирования договора --}}
{{-- Использование: <x-cabinet.contract-modal /> --}}
{{-- JS API: window.openContractModal(contractorId, contract?) --}}
{{-- События: contract:created { contract, contractorId }, contract:updated { contract } --}}
{{-- CSS: resources/css/components/cabinet/contract-modal.scss + calendar.scss --}}

<div class="modal-overlay" id="contract-modal">
  <div class="modal" style="max-width:420px;">
    <div class="modal-head">
      <div>
        <div class="modal-title" id="ct-modal-title">Добавить договор</div>
        <div class="modal-sub">Название, номер и дата договора</div>
      </div>
      <button class="modal-x" id="ct-modal-x">×</button>
    </div>
    <div class="modal-body" style="gap:14px;">
      <div class="m-field">
        <div class="m-field-label">Название <span style="color:var(--red)">*</span></div>
        <input class="m-field-input" id="ct-name" type="text" placeholder="Договор на оказание услуг">
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="m-field">
          <div class="m-field-label">Номер <span style="color:var(--red)">*</span></div>
          <input class="m-field-input" id="ct-number" type="text" placeholder="26-1/2025">
        </div>
        <div class="m-field">
          <div class="m-field-label">Дата <span style="color:var(--red)">*</span></div>
          <div class="date-wrap">
            <svg class="date-ico" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
              <rect x="1.5" y="2.5" width="13" height="12" rx="2"/><path d="M5 1.5v2M11 1.5v2M1.5 6.5h13"/>
            </svg>
            <input class="date-input" id="ct-date" type="text" readonly placeholder="Выберите дату">
            <div class="cal-pop" id="ct-calendar">
              <div class="cal-header">
                <button class="cal-nav" id="ct-cal-prev" type="button">‹</button>
                <span class="cal-month-lbl" id="ct-cal-label"></span>
                <button class="cal-nav" id="ct-cal-next" type="button">›</button>
              </div>
              <div class="cal-grid" id="ct-cal-grid"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-outline" id="ct-cancel">Отмена</button>
      <button class="btn btn-primary" id="ct-save" disabled>Сохранить</button>
    </div>
  </div>
</div>

@once
@push('scripts')
<script>
(function () {
  const CSRF = document.querySelector('meta[name="csrf-token"]').content;

  const MONTHS = ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
  const DAYS   = ['Пн','Вт','Ср','Чт','Пт','Сб','Вс'];

  let _contractorId  = null;
  let _editingId     = null;
  let _selectedDate  = null;
  let _calViewing    = new Date();

  const modal      = document.getElementById('contract-modal');
  const inpName    = document.getElementById('ct-name');
  const inpNum     = document.getElementById('ct-number');
  const ctDateInp  = document.getElementById('ct-date');
  const ctCalPop   = document.getElementById('ct-calendar');
  const btnSave    = document.getElementById('ct-save');

  function formatDateDisplay(d) {
    return d.toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' });
  }
  function formatDateISO(d) {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
  }

  function validate() {
    btnSave.disabled = !(inpName.value.trim() && inpNum.value.trim() && _selectedDate);
  }

  [inpName, inpNum].forEach(el => el.addEventListener('input', validate));

  /* ── Calendar ── */
  function renderCtCal() {
    const y = _calViewing.getFullYear(), m = _calViewing.getMonth();
    document.getElementById('ct-cal-label').textContent = MONTHS[m] + ' ' + y;
    let g = DAYS.map(d => `<div class="cal-dow">${d}</div>`).join('');
    let fd = new Date(y, m, 1).getDay(); if (fd === 0) fd = 7; fd--;
    for (let i = 0; i < fd; i++) g += '<div class="cal-day empty"></div>';
    const tot = new Date(y, m + 1, 0).getDate();
    for (let d = 1; d <= tot; d++) {
      const date = new Date(y, m, d);
      const sel = _selectedDate && date.toDateString() === _selectedDate.toDateString();
      const tod = date.toDateString() === new Date().toDateString();
      g += `<div class="cal-day${sel ? ' selected' : ''}${tod && !sel ? ' today' : ''}" data-y="${y}" data-m="${m}" data-d="${d}">${d}</div>`;
    }
    const grid = document.getElementById('ct-cal-grid');
    grid.innerHTML = g;
    grid.querySelectorAll('.cal-day:not(.empty)').forEach(el => {
      el.addEventListener('click', () => {
        _selectedDate = new Date(+el.dataset.y, +el.dataset.m, +el.dataset.d);
        ctDateInp.value = formatDateDisplay(_selectedDate);
        closeCtCal();
        renderCtCal();
        validate();
      });
    });
  }

  function openCtCal() {
    const rect = ctDateInp.getBoundingClientRect();
    ctCalPop.style.position = 'fixed';
    ctCalPop.style.top  = (rect.bottom + 6) + 'px';
    ctCalPop.style.left = rect.left + 'px';
    ctCalPop.classList.add('open');
    ctDateInp.classList.add('open');
    renderCtCal();
  }

  function closeCtCal() {
    ctCalPop.classList.remove('open');
    ctDateInp.classList.remove('open');
  }

  ctDateInp.addEventListener('click', e => { e.stopPropagation(); ctCalPop.classList.contains('open') ? closeCtCal() : openCtCal(); });
  document.getElementById('ct-cal-prev').addEventListener('click', e => { e.stopPropagation(); _calViewing.setMonth(_calViewing.getMonth() - 1); renderCtCal(); });
  document.getElementById('ct-cal-next').addEventListener('click', e => { e.stopPropagation(); _calViewing.setMonth(_calViewing.getMonth() + 1); renderCtCal(); });
  document.addEventListener('click', e => { if (!ctCalPop.contains(e.target)) closeCtCal(); });

  /* ── Modal open / close ── */
  function closeModal() {
    closeCtCal();
    modal.classList.remove('open');
    _editingId    = null;
    _contractorId = null;
  }

  document.getElementById('ct-cancel').addEventListener('click', closeModal);
  document.getElementById('ct-modal-x').addEventListener('click', closeModal);
  modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

  window.openContractModal = function (contractorId, contract = null) {
    _contractorId = contractorId;
    _editingId    = contract?.id ?? null;

    document.getElementById('ct-modal-title').textContent =
      contract ? 'Редактировать договор' : 'Добавить договор';

    inpName.value = contract?.name   ?? '';
    inpNum.value  = contract?.number ?? '';

    if (contract?.date) {
      _selectedDate = new Date(contract.date + 'T00:00:00');
      _calViewing   = new Date(_selectedDate);
      ctDateInp.value = formatDateDisplay(_selectedDate);
    } else {
      _selectedDate   = null;
      _calViewing     = new Date();
      ctDateInp.value = '';
    }

    validate();
    modal.classList.add('open');
    setTimeout(() => inpName.focus(), 80);
  };

  /* ── Save ── */
  btnSave.addEventListener('click', async function () {
    const payload = {
      name:   inpName.value.trim(),
      number: inpNum.value.trim(),
      date:   formatDateISO(_selectedDate),
    };

    const origHtml     = this.innerHTML;
    const wasEditing   = !!_editingId;
    const contractorId = _contractorId;
    const editingId    = _editingId;

    this.textContent = 'Сохраняем…';
    this.disabled    = true;

    try {
      const url    = wasEditing
        ? '/cabinet/contracts/' + editingId
        : '/cabinet/contractors/' + contractorId + '/contracts';
      const method = wasEditing ? 'PATCH' : 'POST';

      const res  = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(payload),
      });
      const json = await res.json();

      if (res.ok) {
        closeModal();
        document.dispatchEvent(new CustomEvent(
          wasEditing ? 'contract:updated' : 'contract:created',
          { detail: { contract: json.contract, contractorId } }
        ));
      } else {
        const first = json.errors ? Object.values(json.errors)[0] : null;
        alert(Array.isArray(first) ? first[0] : (json.message ?? 'Ошибка сохранения'));
        this.innerHTML = origHtml;
        this.disabled  = false;
      }
    } catch {
      alert('Ошибка соединения');
      this.innerHTML = origHtml;
      this.disabled  = false;
    }
  });
})();
</script>
@endpush
@endonce
