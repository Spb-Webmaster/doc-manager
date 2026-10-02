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
          <x-cabinet.date-picker id="ct-date" placeholder="Выберите дату" />
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

  let _contractorId  = null;
  let _editingId     = null;
  let _selectedDate  = null;
  let _cal           = null;   // экземпляр общего календаря (include/cabinet/calendar.js)

  const modal      = document.getElementById('contract-modal');
  const inpName    = document.getElementById('ct-name');
  const inpNum     = document.getElementById('ct-number');
  const btnSave    = document.getElementById('ct-save');

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

  /* ── Календарь ── */
  // Общий модуль подключается сборкой Vite и выполняется после разбора страницы,
  // поэтому забираем созданный им экземпляр на DOMContentLoaded.
  document.addEventListener('DOMContentLoaded', () => {
    [_cal] = window.cabinetCalendar.init(document.querySelector('[data-input-id="ct-date"]'));
    if (!_cal) return;

    _cal.onSelect = date => { _selectedDate = date; validate(); };
  });

  /* ── Modal open / close ── */
  function closeModal() {
    _cal?.close();
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

    _cal?.setDate(contract?.date ?? null, { silent: true });
    _selectedDate = _cal?.getDate() ?? null;

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
