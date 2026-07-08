@extends('layouts.cabinet')

@section('title', 'Контрагенты — СчётОк')

@section('content')
<div class="main-area contractors-page">

  <header class="topbar">
    <span class="tb-title">Контрагенты</span>
    <div class="tb-right">
      <button class="btn btn-primary btn-sm" id="open-add-modal">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
          <path d="M6.5 1.5v10M1.5 6.5h10"/>
        </svg>
        Добавить контрагента
      </button>
    </div>
  </header>

  <div class="content">

    <!-- Левая колонка: список -->
    <div class="list-col">
      <div class="list-toolbar">
        <div class="search-wrap">
          <svg class="search-ico" width="15" height="15" viewBox="0 0 15 15" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
            <circle cx="6.5" cy="6.5" r="5"/><path d="M10.5 10.5l3 3"/>
          </svg>
          <input class="search-input" id="cp-search" type="text" placeholder="Поиск по названию или ИНН…">
        </div>
        <div class="list-count" id="cp-count"></div>
      </div>
      <div class="cp-list" id="cp-list"></div>
      <div class="add-cp-item" id="add-cp-inline">
        <div class="add-cp-ico">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="var(--accent)" stroke-width="1.6" stroke-linecap="round">
            <path d="M8 2v12M2 8h12"/>
          </svg>
        </div>
        Добавить контрагента
      </div>
    </div>

    <!-- Правая колонка: детали -->
    <div class="detail-col" id="detail-col">
      <div class="detail-empty" id="detail-empty">
        <svg width="64" height="64" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="22" cy="22" r="12"/>
          <circle cx="44" cy="22" r="12"/>
          <path d="M4 54c0-10 8-18 18-18h20c10 0 18 8 18 18"/>
        </svg>
        <div class="detail-empty-title">Выберите контрагента</div>
        <div class="detail-empty-sub">Нажмите на контрагента в списке слева, чтобы увидеть его данные и историю документов</div>
      </div>
      <div id="detail-content" style="display:none;flex-direction:column;flex:1;"></div>
    </div>

  </div>
</div>

<!-- Модальное окно: добавить контрагента -->
<div class="modal-overlay" id="add-modal">
  <div class="modal">
    <div class="modal-head">
      <div>
        <div class="modal-title">Добавить контрагента</div>
        <div class="modal-sub">Введите ИНН — реквизиты заполнятся автоматически из ФНС</div>
      </div>
      <button class="modal-x" data-close="add-modal">×</button>
    </div>
    <div class="modal-body">
      <div class="m-field">
        <div class="m-field-label">ИНН контрагента</div>
        <div class="inn-status-wrap">
          <input class="m-field-input" id="modal-inn" type="text" placeholder="Введите ИНН (10 или 12 цифр)" maxlength="12" autocomplete="off" style="padding-right:44px;">
          <div class="inn-status">
            <div class="spinner" id="modal-spinner"></div>
            <svg id="modal-inn-ok" width="20" height="20" viewBox="0 0 20 20" fill="none" style="display:none">
              <circle cx="10" cy="10" r="9" fill="#E6F7EF"/>
              <path d="M6 10l3 3 5-5" stroke="#159B6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <svg id="modal-inn-err" width="20" height="20" viewBox="0 0 20 20" fill="none" style="display:none">
              <circle cx="10" cy="10" r="9" fill="#FEF0F0"/>
              <path d="M13 7l-6 6M7 7l6 6" stroke="#D63B3B" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
          </div>
        </div>
        <div class="m-field-hint">ИНН для ИП — 12 цифр, для ООО/АО — 10 цифр</div>
      </div>

      <div class="autofill-result" id="modal-result">
        <div class="af-name">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
            <circle cx="7" cy="7" r="6" fill="#159B6A"/>
            <path d="M4.5 7l2 2 3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span id="modal-cp-name">—</span>
        </div>
        <div class="af-row"><span>ИНН: </span><span id="modal-cp-inn">—</span></div>
        <div class="af-row" id="modal-kpp-row"><span>КПП: </span><span id="modal-cp-kpp">—</span></div>
        <div class="af-row"><span id="modal-ogrn-label">ОГРН: </span><span id="modal-cp-ogrn">—</span></div>
        <div class="af-row"><span>Адрес: </span><span id="modal-cp-addr">—</span></div>
      </div>

      <div class="m-field">
        <div class="m-field-label">Email контрагента <span style="color:var(--text-s);font-weight:400;">(необязательно)</span></div>
        <input class="m-field-input" id="modal-email" type="email" placeholder="email@company.ru">
        <div class="m-field-hint">Будет использоваться для отправки документов</div>
      </div>
    </div>
    <div class="modal-foot">
      <a class="btn btn-outline" href="{{ route('cabinet.contractors.create', ['mode' => 'manual']) }}" style="margin-right:auto;">Ввести вручную</a>
      <button class="btn btn-outline" id="modal-cancel">Отмена</button>
      <button class="btn btn-primary" id="modal-save" disabled>Добавить</button>
    </div>
  </div>
</div>

<!-- Модальное окно: редактировать контрагента -->
<div class="modal-overlay" id="edit-modal">
  <div class="modal" style="max-width:560px;">
    <div class="modal-head">
      <div>
        <div class="modal-title">Редактировать контрагента</div>
        <div class="modal-sub" id="edit-modal-inn"></div>
      </div>
      <button class="modal-x" data-close="edit-modal">×</button>
    </div>
    <div class="modal-body" style="max-height:62vh;overflow-y:auto;gap:14px;">
      <div class="m-field">
        <div class="m-field-label">Наименование <span style="color:var(--red)">*</span></div>
        <input class="m-field-input" id="edit-name" type="text" placeholder="ООО «Компания» / ИП Иванов И.О.">
      </div>
      <div class="m-field">
        <div class="m-field-label">Полное наименование <span style="color:var(--text-s);font-weight:400;">(необязательно)</span></div>
        <input class="m-field-input" id="edit-full-name" type="text">
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="m-field">
          <div class="m-field-label">КПП</div>
          <input class="m-field-input" id="edit-kpp" type="text" maxlength="9" placeholder="—">
        </div>
        <div class="m-field">
          <div class="m-field-label">ОГРН</div>
          <input class="m-field-input" id="edit-ogrn" type="text" placeholder="—">
        </div>
      </div>
      <div class="m-field">
        <div class="m-field-label">Юридический адрес</div>
        <input class="m-field-input" id="edit-address" type="text" placeholder="Город, улица, дом">
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="m-field">
          <div class="m-field-label">Email</div>
          <input class="m-field-input" id="edit-email" type="email" placeholder="email@company.ru">
        </div>
        <div class="m-field">
          <div class="m-field-label">Телефон</div>
          <input class="m-field-input imask" id="edit-phone" type="tel" placeholder="+7 ___ ___-__-__">
        </div>
      </div>
      <div class="m-field">
        <div class="m-field-label">Контактное лицо</div>
        <input class="m-field-input" id="edit-contact" type="text" placeholder="Фамилия Имя Отчество">
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="m-field">
          <div class="m-field-label">БИК</div>
          <input class="m-field-input" id="edit-bik" type="text" maxlength="9" inputmode="numeric" placeholder="—">
        </div>
        <div class="m-field">
          <div class="m-field-label">Банк</div>
          <input class="m-field-input" id="edit-bank" type="text" placeholder="—">
        </div>
      </div>
      <div class="m-field">
        <div class="m-field-label">Расчётный счёт</div>
        <input class="m-field-input" id="edit-account" type="text" maxlength="25" inputmode="numeric" placeholder="40802 810 ...">
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-outline" id="edit-cancel">Отмена</button>
      <button class="btn btn-primary" id="edit-save" disabled>Сохранить</button>
    </div>
  </div>
</div>

<x-cabinet.invoice-view-modal />
<x-cabinet.act-view-modal />

<x-cabinet.contract-modal />

<!-- Модальное окно: подтверждение удаления контрагента -->
<div class="modal-overlay" id="delete-cp-modal">
  <div class="modal" style="max-width:440px;">
    <div class="modal-head">
      <div>
        <div class="modal-title">Удалить контрагента?</div>
        <div class="modal-sub" id="del-cp-name" style="font-weight:600;color:var(--text-h);"></div>
      </div>
      <button class="modal-x" data-close="delete-cp-modal">×</button>
    </div>
    <div class="modal-body" style="gap:12px;">
      <p style="font-size:14px;color:var(--text-b);margin:0;line-height:1.6;">Это действие нельзя отменить.</p>
      <div id="del-cp-warning" style="display:none;background:var(--red-lt);border:1px solid rgba(214,59,59,.2);border-radius:var(--rad-sm);padding:12px 14px;">
        <div style="font-size:13px;font-weight:700;color:var(--red);margin-bottom:6px;">Вместе с контрагентом будут удалены:</div>
        <ul id="del-cp-list" style="margin:0;padding-left:18px;font-size:13px;color:var(--text-b);line-height:1.9;"></ul>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-outline" id="del-cp-cancel">Отмена</button>
      <button class="btn" id="del-cp-confirm" style="background:var(--red);color:#fff;">Удалить</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast">
  <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
    <circle cx="7" cy="7" r="6" fill="#159B6A"/>
    <path d="M4 7l2.5 2.5 3.5-3.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>
  <span id="toast-text">Готово</span>
</div>
@endsection

@push('scripts')
<script>
  const CSRF  = document.querySelector('meta[name="csrf-token"]').content;
  const COLORS = ['#2550E2','#159B6A','#6B45D8','#D4700A','#D63B3B','#0891B2'];

  let contractors = @json($contractors->values());
  let activeId = null;
  let contracts    = [];
  let docInvoices  = [];
  let docActs      = [];

  function initials(name) {
    if (!name) return '?';
    let s = name
      .replace(/[«»"']/g, '')
      .replace(/общество\s+с\s+ограниченной\s+ответственностью/gi, '')
      .replace(/открытое\s+акционерное\s+общество/gi, '')
      .replace(/закрытое\s+акционерное\s+общество/gi, '')
      .replace(/публичное\s+акционерное\s+общество/gi, '')
      .replace(/акционерное\s+общество/gi, '')
      .replace(/индивидуальный\s+предприниматель/gi, '')
      .replace(/(^|\s)(ООО|ОАО|ЗАО|ПАО|АО|ГУП|МУП|АНО|НКО|ФГУ|КФХ|ИП|СНТ|ТСЖ)(\s|$)/gi, ' ')
      .trim();
    const words = s.split(/[\s\-]+/).filter(Boolean);
    if (!words.length) return '?';
    if (words.length === 1) {
      const caps = words[0].match(/[А-ЯЁA-Z]/g);
      if (caps && caps.length >= 2) return caps.slice(0, 2).join('');
      return words[0].slice(0, 2).toUpperCase();
    }
    return words.slice(0, 2).map(w => (w[0] || '').toUpperCase()).join('');
  }

  function color(index) {
    return COLORS[index % COLORS.length];
  }

  function escHtml(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  /* ── Render list ── */
  function renderList(query = '') {
    const q = (query || '').trim().toLowerCase();
    const filtered = contractors.filter(c =>
      !q || (c.name || '').toLowerCase().includes(q) || (c.inn || '').includes(q)
    );

    const n = filtered.length;
    const suffix = n === 1 ? 'контрагент' : n < 5 ? 'контрагента' : 'контрагентов';
    document.getElementById('cp-count').textContent = n + ' ' + suffix;

    document.getElementById('cp-list').innerHTML = filtered.map(c => {
      const idx   = contractors.indexOf(c);
      const clr   = color(idx);
      const total = (c.inv_count || 0) + (c.act_count || 0);
      const isAct = activeId === c.id;
      return `<div class="cp-item${isAct ? ' active' : ''}" data-id="${c.id}">
        <div class="cp-ava" style="background:${clr}20;color:${clr};">${escHtml(initials(c.name))}</div>
        <div class="cp-info">
          <div class="cp-name">${escHtml(c.name)}</div>
          <div class="cp-meta">
            <span>ИНН ${escHtml(c.inn)}</span>
            ${c.email ? `<span>· ${escHtml(c.email)}</span>` : ''}
          </div>
        </div>
        <div class="cp-docs-count">
          <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
            <path d="M7 1H3.5A1.5 1.5 0 0 0 2 2.5v7A1.5 1.5 0 0 0 3.5 11h5A1.5 1.5 0 0 0 10 9.5V4z"/>
            <path d="M7 1v3h3"/>
          </svg>
          ${total}
        </div>
      </div>`;
    }).join('');

    document.getElementById('cp-list').querySelectorAll('.cp-item').forEach(el => {
      el.addEventListener('click', () => selectCp(Number(el.dataset.id)));
    });
  }

  /* ── Detail ── */
  function selectCp(id) {
    activeId = id;
    renderList(document.getElementById('cp-search').value);

    const c   = contractors.find(x => x.id === id);
    const idx = contractors.indexOf(c);
    const clr = color(idx);
    const isIp = (c.inn || '').length === 12;
    const total = (c.inv_count || 0) + (c.act_count || 0);

    document.getElementById('detail-empty').style.display = 'none';
    const dc = document.getElementById('detail-content');
    dc.style.display = 'flex';
    document.querySelector('.contractors-page').classList.add('show-detail');

    dc.innerHTML = `
      <div class="detail-head">
        <button type="button" class="cp-back-btn" id="cp-back-btn">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 2.5L3.5 7l5 4.5"/></svg>
          Контрагенты
        </button>
        <div class="detail-ava" style="background:${clr}20;color:${clr};">${escHtml(initials(c.name))}</div>
        <div class="detail-head-info">
          <div class="detail-name">${escHtml(c.name)}</div>
          <div class="detail-inn">ИНН ${escHtml(c.inn)}${c.kpp ? ' · КПП ' + escHtml(c.kpp) : ''}</div>
        </div>
        <div class="detail-head-btns">
          <a href="{{ route('cabinet.invoices.create') }}?contractor_id=${c.id}" class="btn btn-primary btn-sm">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M7.5 2H3a1.5 1.5 0 0 0-1.5 1.5v7A1.5 1.5 0 0 0 3 12h7a1.5 1.5 0 0 0 1.5-1.5V5.5M7.5 2v3.5H11M6.5 7v3M5 8.5h3"/>
            </svg>
            Создать счёт
          </a>
          <a href="{{ route('cabinet.acts.create') }}?contractor_id=${c.id}" class="btn btn-sm btn-outline" style="color:var(--green);border-color:var(--green);">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M7.5 2H3a1.5 1.5 0 0 0-1.5 1.5v7A1.5 1.5 0 0 0 3 12h7a1.5 1.5 0 0 0 1.5-1.5V5.5M7.5 2v3.5H11M6.5 7v3M5 8.5h3"/>
            </svg>
            Создать акт
          </a>
          <button class="btn btn-sm btn-outline" onclick="openContractModal(${id})">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M7 1H3a1.5 1.5 0 0 0-1.5 1.5v9A1.5 1.5 0 0 0 3 13h7a1.5 1.5 0 0 0 1.5-1.5V5.5M7 1v3h3.5M4.5 8h4M4.5 10h2.5"/>
            </svg>
            Создать договор
          </button>
          <button class="btn btn-sm btn-outline" onclick="openEditModal(${id})">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 2l2 2-6 6H3V8z"/>
            </svg>
            Изменить
          </button>
          <button class="btn btn-sm btn-danger-sm" onclick="deleteCp(${id})">Удалить</button>
        </div>
      </div>

      <div class="detail-body">
        <div class="cp-stats-row">
          <div class="cp-stat">
            <div class="cp-stat-val">${total}</div>
            <div class="cp-stat-lbl">Документов</div>
          </div>
          <div class="cp-stat">
            <div class="cp-stat-val" style="color:var(--accent);">${c.inv_count || 0}</div>
            <div class="cp-stat-lbl">Счетов</div>
          </div>
          <div class="cp-stat">
            <div class="cp-stat-val" style="color:var(--green);">${c.act_count || 0}</div>
            <div class="cp-stat-lbl">Актов</div>
          </div>
        </div>

        <div class="info-card">
          <div class="info-card-head">Реквизиты</div>
          <div class="info-card-body">
            ${c.full_name ? `<div class="info-row"><div class="info-key">Полное</div><div class="info-val">${escHtml(c.full_name)}</div></div>` : ''}
            <div class="info-row"><div class="info-key">ИНН</div><div class="info-val">${escHtml(c.inn)}</div></div>
            ${c.kpp  ? `<div class="info-row"><div class="info-key">КПП</div><div class="info-val">${escHtml(c.kpp)}</div></div>` : ''}
            ${c.ogrn ? `<div class="info-row"><div class="info-key">${isIp ? 'ОГРНИП' : 'ОГРН'}</div><div class="info-val">${escHtml(c.ogrn)}</div></div>` : ''}
            ${c.address ? `<div class="info-row"><div class="info-key">Адрес</div><div class="info-val">${escHtml(c.address)}</div></div>` : ''}
            ${c.email   ? `<div class="info-row"><div class="info-key">Email</div><div class="info-val">${escHtml(c.email)}</div></div>` : ''}
          </div>
        </div>

        <div class="doc-mini-table">
          <div class="doc-tabs">
            <button class="doc-tab active" data-tab="inv">Счета <span class="doc-tab-cnt" id="docs-cnt-inv">0</span></button>
            <button class="doc-tab" data-tab="act">Акты <span class="doc-tab-cnt" id="docs-cnt-act">0</span></button>
          </div>
          <div class="doc-pane" id="docs-pane-inv"><div class="doc-empty">Загрузка…</div></div>
          <div class="doc-pane" id="docs-pane-act" style="display:none"><div class="doc-empty">Загрузка…</div></div>
        </div>

        <div class="info-card">
          <div class="contracts-head">
            <span>Договоры</span>
            <button class="btn btn-sm btn-outline" style="padding:5px 12px;font-size:12px;" onclick="openContractModal(${id})">
              <svg width="11" height="11" viewBox="0 0 11 11" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" style="margin-right:4px;">
                <path d="M5.5 1v9M1 5.5h9"/>
              </svg>Добавить
            </button>
          </div>
          <div id="contracts-body"><div class="doc-empty">Загрузка…</div></div>
        </div>
      </div>`;

    loadContracts(id);
    setupDocTabs();
    loadDocs(id);
  }

  function pluralize(n, one, few, many) {
    const m10 = n % 10, m100 = n % 100;
    if (m10 === 1 && m100 !== 11) return `${n} ${one}`;
    if (m10 >= 2 && m10 <= 4 && (m100 < 10 || m100 >= 20)) return `${n} ${few}`;
    return `${n} ${many}`;
  }

  let deletingCpId = null;

  function deleteCp(id) {
    const c = contractors.find(x => x.id === id);
    if (!c) return;
    deletingCpId = id;

    document.getElementById('del-cp-name').textContent = c.name;

    const items = [];
    if (c.inv_count > 0) items.push(pluralize(c.inv_count, 'счёт', 'счёта', 'счетов'));
    if (c.act_count > 0) items.push(pluralize(c.act_count, 'акт', 'акта', 'актов'));
    const ctCount = (activeId === id) ? contracts.length : 0;
    if (ctCount > 0) items.push(pluralize(ctCount, 'договор', 'договора', 'договоров'));

    const warning = document.getElementById('del-cp-warning');
    const list    = document.getElementById('del-cp-list');
    if (items.length) {
      list.innerHTML = items.map(s => `<li>${s}</li>`).join('');
      warning.style.display = '';
    } else {
      warning.style.display = 'none';
    }

    document.getElementById('delete-cp-modal').classList.add('open');
  }
  window.deleteCp = deleteCp;

  document.getElementById('del-cp-cancel').addEventListener('click', () => {
    document.getElementById('delete-cp-modal').classList.remove('open');
    deletingCpId = null;
  });

  document.getElementById('del-cp-confirm').addEventListener('click', async function() {
    if (!deletingCpId) return;
    const id = deletingCpId;
    document.getElementById('delete-cp-modal').classList.remove('open');
    deletingCpId = null;

    try {
      const res = await fetch('{{ url('/cabinet/contractors') }}/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      });
      if (res.ok) {
        contractors = contractors.filter(c => c.id !== id);
        if (activeId === id) {
          activeId = null;
          document.getElementById('detail-empty').style.display = 'flex';
          document.getElementById('detail-content').style.display = 'none';
        }
        renderList(document.getElementById('cp-search').value);
        showToast('Контрагент удалён');
      } else {
        showToast('Не удалось удалить контрагента');
      }
    } catch {
      showToast('Ошибка соединения');
    }
  });

  /* ── Search ── */
  document.getElementById('cp-search').addEventListener('input', function() {
    renderList(this.value);
  });

  /* ── Мобильный drill-down: назад к списку ── */
  document.getElementById('detail-col').addEventListener('click', function(e) {
    if (e.target.closest('#cp-back-btn')) {
      document.querySelector('.contractors-page').classList.remove('show-detail');
    }
  });

  /* ── Modal ── */
  let modalData = null;
  let innTimer  = null;

  function openModal() {
    document.getElementById('add-modal').classList.add('open');
    document.getElementById('modal-inn').value = '';
    document.getElementById('modal-email').value = '';
    document.getElementById('modal-result').classList.remove('show');
    document.getElementById('modal-inn').classList.remove('valid','invalid');
    document.getElementById('modal-inn-ok').style.display  = 'none';
    document.getElementById('modal-inn-err').style.display = 'none';
    document.getElementById('modal-spinner').style.display = 'none';
    document.getElementById('modal-save').disabled = true;
    modalData = null;
    setTimeout(() => document.getElementById('modal-inn').focus(), 100);
  }

  function closeModal() {
    document.getElementById('add-modal').classList.remove('open');
  }

  document.getElementById('open-add-modal').addEventListener('click', openModal);
  document.getElementById('add-cp-inline').addEventListener('click', openModal);
  document.getElementById('modal-cancel').addEventListener('click', closeModal);

  document.getElementById('modal-inn').addEventListener('input', function() {
    const v = this.value.replace(/\D/g, '');
    this.value = v;
    this.classList.remove('valid','invalid');
    document.getElementById('modal-inn-ok').style.display  = 'none';
    document.getElementById('modal-inn-err').style.display = 'none';
    document.getElementById('modal-result').classList.remove('show');
    document.getElementById('modal-save').disabled = true;
    modalData = null;
    clearTimeout(innTimer);
    if (v.length >= 10) {
      document.getElementById('modal-spinner').style.display = 'block';
      innTimer = setTimeout(() => doModalLookup(v), 700);
    } else {
      document.getElementById('modal-spinner').style.display = 'none';
    }
  });

  async function doModalLookup(inn) {
    document.getElementById('modal-spinner').style.display = 'none';
    try {
      const res  = await fetch('{{ route('dadata.party') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ inn }),
      });
      const json = await res.json();

      if (!res.ok) {
        document.getElementById('modal-inn').classList.add('invalid');
        document.getElementById('modal-inn-err').style.display = 'block';
        return;
      }

      modalData = { inn, name: json.short || json.name, full_name: json.name, kpp: json.kpp, ogrn: json.ogrn, address: json.address, email: json.email };
      const isIp = inn.length === 12;

      document.getElementById('modal-inn').classList.add('valid');
      document.getElementById('modal-inn-ok').style.display = 'block';
      document.getElementById('modal-cp-name').textContent  = json.short || json.name;
      document.getElementById('modal-cp-inn').textContent   = inn;

      if (json.kpp) {
        document.getElementById('modal-kpp-row').style.display = '';
        document.getElementById('modal-cp-kpp').textContent = json.kpp;
      } else {
        document.getElementById('modal-kpp-row').style.display = 'none';
      }

      document.getElementById('modal-ogrn-label').textContent = isIp ? 'ОГРНИП: ' : 'ОГРН: ';
      document.getElementById('modal-cp-ogrn').textContent = json.ogrn || '—';
      document.getElementById('modal-cp-addr').textContent = json.address || '—';
      document.getElementById('modal-result').classList.add('show');
      document.getElementById('modal-save').disabled = false;

      if (json.email && !document.getElementById('modal-email').value) {
        document.getElementById('modal-email').value = json.email;
      }
    } catch {
      document.getElementById('modal-inn').classList.add('invalid');
      document.getElementById('modal-inn-err').style.display = 'block';
    }
  }

  document.getElementById('modal-save').addEventListener('click', async function() {
    if (!modalData) return;
    if (contractors.find(c => c.inn === modalData.inn)) {
      showToast('Этот контрагент уже добавлен');
      closeModal();
      return;
    }

    const email    = document.getElementById('modal-email').value.trim();
    const origHtml = this.innerHTML;
    this.textContent = 'Добавляем…';
    this.disabled = true;

    try {
      const res = await fetch('{{ route('cabinet.contractors.store') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ ...modalData, email }),
      });
      const json = await res.json();

      if (res.ok) {
        contractors.unshift(json.contractor);
        renderList(document.getElementById('cp-search').value);
        closeModal();
        showToast('Контрагент добавлен');
        selectCp(json.contractor.id);
      } else {
        const first = json.errors ? Object.values(json.errors)[0] : null;
        showToast(Array.isArray(first) ? first[0] : (json.error ?? 'Ошибка сохранения'));
      }
    } catch {
      showToast('Ошибка соединения');
    } finally {
      this.innerHTML = origHtml;
      this.disabled = false;
    }
  });

  /* ── Toast ── */
  let toastTimer;
  function showToast(msg) {
    document.getElementById('toast-text').textContent = msg;
    const t = document.getElementById('toast');
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 2500);
  }

  /* ── Edit Modal ── */
  let editingId = null;
  const EDIT_BASE = '{{ url('/cabinet/contractors') }}';


  function openEditModal(id) {
    const c = contractors.find(x => x.id === id);
    if (!c) return;
    editingId = id;

    document.getElementById('edit-modal-inn').textContent   = 'ИНН ' + c.inn;
    document.getElementById('edit-name').value              = c.name || '';
    document.getElementById('edit-full-name').value         = c.full_name || '';
    document.getElementById('edit-kpp').value               = c.kpp || '';
    document.getElementById('edit-ogrn').value              = c.ogrn || '';
    document.getElementById('edit-address').value           = c.address || '';
    document.getElementById('edit-email').value             = c.email || '';
    document.getElementById('edit-phone')._iMask.value      = c.phone || '';
    document.getElementById('edit-contact').value           = c.person_contract || '';
    document.getElementById('edit-bik').value               = c.bik || '';
    document.getElementById('edit-bank').value              = c.bank || '';
    document.getElementById('edit-account').value           = c.payment_account || '';
    document.getElementById('edit-save').disabled           = !(c.name || '').trim();
    document.getElementById('edit-modal').classList.add('open');
    setTimeout(() => document.getElementById('edit-name').focus(), 80);
  }
  window.openEditModal = openEditModal;

  function closeEditModal() {
    document.getElementById('edit-modal').classList.remove('open');
    editingId = null;
  }

  document.getElementById('edit-name').addEventListener('input', function() {
    document.getElementById('edit-save').disabled = !this.value.trim();
  });
  document.getElementById('edit-cancel').addEventListener('click', closeEditModal);

  document.getElementById('edit-save').addEventListener('click', async function() {
    if (!editingId) return;
    const origHtml = this.innerHTML;
    this.textContent = 'Сохраняем…';
    this.disabled = true;

    const payload = {
      name:            document.getElementById('edit-name').value.trim(),
      full_name:       document.getElementById('edit-full-name').value.trim() || null,
      kpp:             document.getElementById('edit-kpp').value.trim() || null,
      ogrn:            document.getElementById('edit-ogrn').value.trim() || null,
      address:         document.getElementById('edit-address').value.trim() || null,
      email:           document.getElementById('edit-email').value.trim() || null,
      phone:           document.getElementById('edit-phone')._iMask?.unmaskedValue || null,
      person_contract: document.getElementById('edit-contact').value.trim() || null,
      bik:             document.getElementById('edit-bik').value.trim() || null,
      bank:            document.getElementById('edit-bank').value.trim() || null,
      payment_account: document.getElementById('edit-account').value.trim() || null,
    };

    try {
      const res  = await fetch(EDIT_BASE + '/' + editingId, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(payload),
      });
      const json = await res.json();

      if (res.ok) {
        const idx = contractors.findIndex(x => x.id === editingId);
        if (idx !== -1) contractors[idx] = { ...contractors[idx], ...json.contractor };
        renderList(document.getElementById('cp-search').value);
        if (activeId === editingId) selectCp(editingId);
        closeEditModal();
        showToast('Контрагент обновлён');
      } else {
        const first = json.errors ? Object.values(json.errors)[0] : null;
        showToast(Array.isArray(first) ? first[0] : (json.message ?? 'Ошибка сохранения'));
        this.innerHTML = origHtml;
        this.disabled  = false;
      }
    } catch {
      showToast('Ошибка соединения');
      this.innerHTML = origHtml;
      this.disabled  = false;
    }
  });

  /* ── Docs (invoices / acts) ── */
  function fmtMoney(n) {
    return new Intl.NumberFormat('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n) + ' ₽';
  }

  const INV_STATUS = {
    draft:     ['Черновик',  'badge-draft'],
    sent:      ['Отправлен', 'badge-sent'],
    paid:      ['Оплачен',   'badge-paid'],
    cancelled: ['Отменён',   'badge-cancelled'],
  };
  const ACT_STATUS = {
    draft:     ['Черновик',  'badge-draft'],
    sent:      ['Отправлен', 'badge-sent'],
    signed:    ['Подписан',  'badge-signed'],
    cancelled: ['Отменён',   'badge-cancelled'],
  };

  function renderInvoicePane(items) {
    const pane = document.getElementById('docs-pane-inv');
    if (!pane) return;
    if (!items.length) {
      pane.innerHTML = '<div class="doc-empty">Счетов пока нет</div>';
      return;
    }
    const rows = items.map(it => {
      const parts = (it.date || '').split('-');
      const dateStr = parts[2] ? `${parts[2]}.${parts[1]}.${parts[0]}` : '—';
      const basis = it.contract_name ? escHtml(it.contract_name) : 'Без договора';
      return `<div class="doc-mini-row doc-inv-row">
        <div class="dmr-num"><a class="dmr-link" href="#" onclick="openInvoiceModal(${it.id});return false;">№ ${escHtml(it.number)}</a></div>
        <div class="dmr-date">${dateStr}</div>
        <div class="dmr-sum">${fmtMoney(it.total)}</div>
        <div class="dmr-basis">${basis}</div>
        <div class="dmr-del"><a href="/cabinet/invoices/${it.id}/pdf" target="_blank" class="btn btn-sm btn-outline btn-pdf" title="PDF"><svg width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 1.5v6M3.5 5.5 6 8l2.5-2.5M1.5 10.5h9"/></svg></a><button class="btn btn-sm btn-danger-sm" onclick="deleteInvoice(${it.id})">Удалить</button></div>
      </div>`;
    }).join('');
    pane.innerHTML = `<div class="doc-mini-head doc-inv-head"><span>Номер</span><span>Дата</span><span style="text-align:right;">Сумма</span><span>Договор</span><span></span></div>${rows}`;
  }

  function renderActPane(items) {
    const pane = document.getElementById('docs-pane-act');
    if (!pane) return;
    if (!items.length) {
      pane.innerHTML = '<div class="doc-empty">Актов пока нет</div>';
      return;
    }
    const rows = items.map(it => {
      const parts = (it.date || '').split('-');
      const dateStr = parts[2] ? `${parts[2]}.${parts[1]}.${parts[0]}` : '—';
      return `<div class="doc-mini-row doc-inv-row">
        <div class="dmr-num"><a class="dmr-link" href="#" style="color:var(--green);" onclick="openActModal(${it.id});return false;">№ ${escHtml(it.number)}</a></div>
        <div class="dmr-date">${dateStr}</div>
        <div class="dmr-sum">${fmtMoney(it.total)}</div>
        <div class="dmr-basis" style="color:var(--text-s);">${escHtml(it.basis || '')}</div>
        <div class="dmr-del"><a href="/cabinet/acts/${it.id}/pdf" target="_blank" class="btn btn-sm btn-outline btn-pdf" title="PDF"><svg width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 1.5v6M3.5 5.5 6 8l2.5-2.5M1.5 10.5h9"/></svg></a><button class="btn btn-sm btn-danger-sm" onclick="deleteAct(${it.id})">Удалить</button></div>
      </div>`;
    }).join('');
    pane.innerHTML = `<div class="doc-mini-head doc-inv-head"><span>Номер</span><span>Дата</span><span style="text-align:right;">Сумма</span><span>Основание</span><span></span></div>${rows}`;
  }

  async function loadDocs(contractorId) {
    docInvoices = [];
    docActs     = [];
    try {
      const [invRes, actRes] = await Promise.all([
        fetch('/cabinet/contractors/' + contractorId + '/invoices', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } }),
        fetch('/cabinet/contractors/' + contractorId + '/acts',     { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } }),
      ]);
      docInvoices = invRes.ok ? await invRes.json() : [];
      docActs     = actRes.ok ? await actRes.json() : [];
    } catch {}

    const cntInv = document.getElementById('docs-cnt-inv');
    const cntAct = document.getElementById('docs-cnt-act');
    if (cntInv) cntInv.textContent = docInvoices.length;
    if (cntAct) cntAct.textContent = docActs.length;

    renderInvoicePane(docInvoices);
    renderActPane(docActs);
  }

  function setupDocTabs() {
    document.querySelectorAll('.doc-tab').forEach(btn => {
      btn.addEventListener('click', function () {
        document.querySelectorAll('.doc-tab').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const tab = this.dataset.tab;
        document.getElementById('docs-pane-inv').style.display = tab === 'inv' ? '' : 'none';
        document.getElementById('docs-pane-act').style.display = tab === 'act' ? '' : 'none';
      });
    });
  }

  /* ── Contracts ── */
  function fmtDate(d) {
    if (!d) return '—';
    const [y, m, day] = d.split('-');
    return `${day}.${m}.${y}`;
  }

  async function loadContracts(contractorId) {
    try {
      const res = await fetch('/cabinet/contractors/' + contractorId + '/contracts', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      });
      contracts = res.ok ? await res.json() : [];
    } catch {
      contracts = [];
    }
    renderContracts();
  }

  function renderContracts() {
    const body = document.getElementById('contracts-body');
    if (!body) return;
    if (!contracts.length) {
      body.innerHTML = '<div class="doc-empty">Договоров пока нет</div>';
      return;
    }
    body.innerHTML = contracts.map(ct => `
      <div class="contract-row" data-id="${ct.id}">
        <div class="contract-drag-handle" title="Перетащить"><svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor"><circle cx="3" cy="2.5" r="1.2"/><circle cx="7" cy="2.5" r="1.2"/><circle cx="3" cy="7" r="1.2"/><circle cx="7" cy="7" r="1.2"/><circle cx="3" cy="11.5" r="1.2"/><circle cx="7" cy="11.5" r="1.2"/></svg></div>
        <div class="contract-row-main">
          <div class="contract-name">${escHtml(ct.name)}</div>
          <div class="contract-meta">№ ${escHtml(ct.number)} · ${fmtDate(ct.date)}</div>
        </div>
        <div class="contract-row-actions">
          <button class="btn btn-sm btn-outline" onclick="editContract(${ct.id})">Изменить</button>
          <button class="btn btn-sm btn-danger-sm" onclick="deleteContract(${ct.id})">Удалить</button>
        </div>
      </div>
    `).join('');
    document.querySelectorAll('#contracts-body .contract-row').forEach(contractSort.attach);
  }

  /* ── Contract drag & drop ── */
  const contractSort = makeSortable({
    getContainer: () => document.getElementById('contracts-body'),
    itemSel:      '.contract-row',
    handleSel:    '.contract-drag-handle',
    saveUrl:      '{{ route('cabinet.contracts.reorder') }}',
    csrf:         CSRF,
    getId:        el => parseInt(el.dataset.id),
  });

  /* ── Contract modal (компонент x-cabinet.contract-modal) ── */
  window.editContract = function (contractId) {
    const ct = contracts.find(c => c.id === contractId);
    if (ct) openContractModal(activeId, ct);
  };

  document.addEventListener('contract:created', ({ detail }) => {
    contracts.unshift(detail.contract);
    renderContracts();
    showToast('Договор добавлен');
  });

  document.addEventListener('contract:updated', ({ detail }) => {
    const idx = contracts.findIndex(c => c.id === detail.contract.id);
    if (idx !== -1) contracts[idx] = detail.contract;
    renderContracts();
    showToast('Договор обновлён');
  });

  async function deleteInvoice(id) {
    if (!confirm('Удалить счёт? Это действие нельзя отменить.')) return;
    try {
      const res = await fetch('/cabinet/invoices/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      });
      if (res.ok) {
        docInvoices = docInvoices.filter(i => i.id !== id);
        const cntInv = document.getElementById('docs-cnt-inv');
        if (cntInv) cntInv.textContent = docInvoices.length;
        renderInvoicePane(docInvoices);
        const cp = contractors.find(c => c.id === activeId);
        if (cp) { cp.inv_count = Math.max(0, (cp.inv_count || 1) - 1); renderList(document.getElementById('cp-search').value); }
        showToast('Счёт удалён');
      } else {
        showToast('Не удалось удалить счёт');
      }
    } catch {
      showToast('Ошибка соединения');
    }
  }
  window.deleteInvoice = deleteInvoice;

  async function deleteAct(id) {
    if (!confirm('Удалить акт? Это действие нельзя отменить.')) return;
    try {
      const res = await fetch('/cabinet/acts/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      });
      if (res.ok) {
        docActs = docActs.filter(a => a.id !== id);
        const cntAct = document.getElementById('docs-cnt-act');
        if (cntAct) cntAct.textContent = docActs.length;
        renderActPane(docActs);
        const cp = contractors.find(c => c.id === activeId);
        if (cp) { cp.act_count = Math.max(0, (cp.act_count || 1) - 1); renderList(document.getElementById('cp-search').value); }
        showToast('Акт удалён');
      } else {
        showToast('Не удалось удалить акт');
      }
    } catch {
      showToast('Ошибка соединения');
    }
  }
  window.deleteAct = deleteAct;

  async function deleteContract(id) {
    if (!confirm('Удалить договор? Это действие нельзя отменить.')) return;
    try {
      const res = await fetch('/cabinet/contracts/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      });
      if (res.ok) {
        contracts = contracts.filter(c => c.id !== id);
        renderContracts();
        showToast('Договор удалён');
      } else {
        showToast('Не удалось удалить договор');
      }
    } catch {
      showToast('Ошибка соединения');
    }
  }
  window.deleteContract = deleteContract;

  /* ── Init ── */
  renderList();

  const openId = +new URLSearchParams(location.search).get('open');
  if (openId) selectCp(openId);

  document.querySelectorAll('[data-close]').forEach(btn => {
    btn.addEventListener('click', () => document.getElementById(btn.dataset.close)?.classList.remove('open'));
  });
</script>
@endpush
