@extends('layouts.cabinet')

@section('title', 'Новый контрагент — СчётОк')

@push('styles')
<style>
  .main { overflow: hidden; }

  .topbar {
    padding: 0 0 0 28px !important;
    gap: 0 !important;
  }
  .tb-breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-s); flex: 1; }
  .tb-breadcrumb a { color: var(--text-s); text-decoration: none; }
  .tb-breadcrumb a:hover { color: var(--text-h); }
  .tb-breadcrumb-sep { opacity: .4; }
  .tb-current { font-weight: 600; color: var(--text-h); }
  .topbar-actions { display: flex; align-items: center; border-left: 1px solid var(--border); height: 64px; flex-shrink: 0; }
  .tb-btn { display: flex; align-items: center; gap: 7px; padding: 0 22px; height: 64px; font-family: inherit; font-size: 14px; font-weight: 500; cursor: pointer; border: none; text-decoration: none; transition: background .14s; white-space: nowrap; border-left: 1px solid var(--border); line-height: 1; }
  .tb-btn:first-child { border-left: none; }
  .tb-btn-ghost { background: transparent; color: var(--text-b); }
  .tb-btn-ghost:hover { background: var(--bg); }
  .tb-btn-primary { background: var(--accent); color: #fff; }
  .tb-btn-primary:hover { background: var(--accent-hv); }
  .tb-btn-primary:disabled { opacity: .5; cursor: not-allowed; pointer-events: none; }

  .create-layout { flex: 1; display: grid; grid-template-columns: 600px 1fr; overflow: hidden; }
  .form-col { overflow-y: auto; border-right: 1px solid var(--border); background: var(--surface); padding: 24px 28px 48px; }
  .preview-col { overflow-y: auto; background: var(--bg); padding: 28px; display: flex; flex-direction: column; align-items: center; }

  .form-section { margin-bottom: 24px; }
  .form-section.disabled { opacity: .45; pointer-events: none; }
  .fs-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
  .fs-title { display: flex; align-items: center; gap: 9px; font-size: 14px; font-weight: 700; color: var(--text-h); letter-spacing: -.2px; }
  .fs-num { width: 22px; height: 22px; border-radius: 6px; background: var(--accent-lt); color: var(--accent); font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .fs-num.done { background: var(--green-lt); color: var(--green); }
  .fs-hint { font-size: 12.5px; color: var(--text-s); font-weight: 400; }
  .form-divider { height: 1px; background: var(--border); margin-bottom: 24px; }

  .inn-wrap { position: relative; margin-bottom: 10px; }
  .inn-input { width: 100%; padding: 12px 48px 12px 14px; border: 1.5px solid var(--border); border-radius: var(--rad-sm); font-family: inherit; font-size: 16px; font-weight: 600; color: var(--text-h); background: var(--bg); outline: none; letter-spacing: .3px; transition: border-color .18s, box-shadow .18s, background .18s; }
  .inn-input::placeholder { font-weight: 400; letter-spacing: 0; color: var(--text-s); }
  .inn-input:focus { border-color: var(--accent); background: var(--surface); box-shadow: 0 0 0 3px rgba(37,80,226,.12); }
  .inn-input.valid   { border-color: var(--green); background: var(--surface); }
  .inn-input.invalid { border-color: var(--red);   background: var(--red-lt); }
  .inn-status { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; }
  .spinner-ring { width: 18px; height: 18px; border: 2px solid var(--border); border-top-color: var(--accent); border-radius: 50%; animation: spin .7s linear infinite; display: none; }
  @keyframes spin { to { transform: rotate(360deg); } }
  .inn-msg { font-size: 12px; min-height: 16px; }
  .inn-msg.err  { color: var(--red); }
  .inn-msg.ok   { color: var(--green); }
  .inn-msg.idle { color: var(--text-s); }

  .field-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
  .field:last-child { margin-bottom: 0; }
  .field-label { font-size: 12.5px; font-weight: 600; color: var(--text-s); text-transform: uppercase; letter-spacing: .5px; }
  .field-label .opt { color: var(--text-s); font-weight: 400; text-transform: none; letter-spacing: 0; font-size: 11.5px; }
  .field-input { width: 100%; padding: 10px 13px; border: 1.5px solid var(--border); border-radius: var(--rad-sm); font-family: inherit; font-size: 14px; color: var(--text-h); background: var(--bg); outline: none; transition: border-color .16s, box-shadow .16s, background .16s; }
  .field-input::placeholder { color: var(--text-s); }
  .field-input:focus { border-color: var(--accent); background: var(--surface); box-shadow: 0 0 0 3px rgba(37,80,226,.10); }
  .field-input[readonly] { background: var(--bg); color: var(--text-b); cursor: default; }
  .field-input[readonly]:focus { border-color: var(--border); box-shadow: none; }
  textarea.field-input { resize: vertical; min-height: 64px; line-height: 1.5; }

  .auto-badge { font-size: 10px; font-weight: 700; color: var(--green); background: var(--green-lt); padding: 2px 7px; border-radius: 5px; text-transform: uppercase; letter-spacing: .4px; }

  .tab-group { display: flex; border: 1.5px solid var(--border); border-radius: var(--rad-sm); overflow: hidden; margin-bottom: 14px; }
  .tab-btn { flex: 1; padding: 9px; border: none; background: transparent; font-family: inherit; font-size: 13px; font-weight: 500; color: var(--text-s); cursor: pointer; transition: background .14s, color .14s; }
  .tab-btn.active { background: var(--accent); color: #fff; }
  .tab-btn:not(.active):hover { background: var(--bg); color: var(--text-h); }

  .preview-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--text-s); margin-bottom: 14px; display: flex; align-items: center; gap: 8px; width: 100%; max-width: 440px; }
  .preview-label::after { content: ''; flex: 1; height: 1px; background: var(--border); }

  .cp-card { background: var(--surface); width: 100%; max-width: 440px; border: 1px solid var(--border); border-radius: 16px; box-shadow: 0 4px 24px rgba(15,22,40,.06); overflow: hidden; }
  .cp-card-head { padding: 22px 24px; display: flex; align-items: center; gap: 16px; border-bottom: 1px solid var(--border); }
  .cp-card-ava { width: 58px; height: 58px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; flex-shrink: 0; background: var(--accent-lt); color: var(--accent); transition: background .2s, color .2s; }
  .cp-card-name { font-size: 17px; font-weight: 700; color: var(--text-h); letter-spacing: -.3px; margin-bottom: 3px; }
  .cp-card-type { display: inline-flex; align-items: center; padding: 2px 9px; border-radius: 6px; font-size: 11.5px; font-weight: 600; background: var(--bg); color: var(--text-s); }
  .cp-card-body { padding: 6px 24px 18px; }
  .cp-card-row { display: flex; align-items: flex-start; gap: 12px; padding: 11px 0; border-bottom: 1px solid var(--border); }
  .cp-card-row:last-child { border-bottom: none; }
  .cp-card-k { width: 92px; flex-shrink: 0; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--text-s); padding-top: 2px; }
  .cp-card-v { font-size: 13.5px; font-weight: 500; color: var(--text-h); flex: 1; }
  .cp-card-v.empty { color: #C0C7D6; font-weight: 400; font-style: italic; }

  .cp-card-empty { padding: 48px 32px; display: flex; flex-direction: column; align-items: center; gap: 12px; text-align: center; }
  .cp-card-empty svg { opacity: .2; }
  .cp-card-empty-t { font-size: 14px; font-weight: 600; color: var(--text-s); }
  .cp-card-empty-s { font-size: 12.5px; color: var(--text-s); max-width: 240px; }

  .preview-foot { width: 100%; max-width: 440px; margin-top: 14px; font-size: 12px; color: var(--text-s); text-align: center; line-height: 1.6; }

  .toast { position: fixed; bottom: 28px; left: 50%; transform: translateX(-50%) translateY(20px); background: var(--dark); color: #fff; padding: 11px 20px; border-radius: 10px; font-size: 14px; font-weight: 500; opacity: 0; transition: opacity .25s, transform .25s; pointer-events: none; z-index: 9999; white-space: nowrap; display: flex; align-items: center; gap: 8px; }
  .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
  .toast svg { color: var(--green); }
</style>
@endpush

@section('content')
<header class="topbar">
  <div class="tb-breadcrumb">
    <a href="{{ route('cabinet.contractors') }}">Контрагенты</a>
    <span class="tb-breadcrumb-sep">›</span>
    <span class="tb-current">Новый контрагент</span>
  </div>
  <div class="topbar-actions">
    <a class="tb-btn tb-btn-ghost" href="{{ route('cabinet.contractors') }}">Отмена</a>
    <button class="tb-btn tb-btn-primary" id="save-btn" disabled>
      <svg width="15" height="15" viewBox="0 0 15 15" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 7.5l3 3 6-6"/>
      </svg>
      Сохранить контрагента
    </button>
  </div>
</header>

<div class="create-layout">

  <!-- ── Форма ── -->
  <div class="form-col">

    <!-- 1. ИНН -->
    <div class="form-section">
      <div class="fs-head">
        <div class="fs-title"><div class="fs-num" id="s1-num">1</div>ИНН контрагента</div>
      </div>
      <div class="tab-group" id="mode-tabs">
        <button class="tab-btn active" data-mode="auto">Найти по ИНН</button>
        <button class="tab-btn" data-mode="manual">Ввести вручную</button>
      </div>

      <div id="pane-auto">
        <div class="inn-wrap">
          <input class="inn-input" id="inn" type="text" placeholder="Введите ИНН (10 или 12 цифр)" maxlength="12" autocomplete="off">
          <div class="inn-status">
            <div class="spinner-ring" id="inn-spinner"></div>
            <svg id="inn-ok" width="20" height="20" viewBox="0 0 20 20" fill="none" style="display:none">
              <circle cx="10" cy="10" r="9" fill="#E6F7EF"/>
              <path d="M6 10l3 3 5-5" stroke="#159B6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <svg id="inn-err" width="20" height="20" viewBox="0 0 20 20" fill="none" style="display:none">
              <circle cx="10" cy="10" r="9" fill="#FEF0F0"/>
              <path d="M13 7l-6 6M7 7l6 6" stroke="#D63B3B" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
          </div>
        </div>
        <div class="inn-msg idle" id="inn-msg">Реквизиты подтянутся автоматически из ФНС</div>
        <div id="inn-duplicate-warn" style="display:none; margin-top:10px; background:var(--red-lt); border:1px solid rgba(214,59,59,.2); border-radius:var(--rad-sm); padding:11px 14px; font-size:13px; color:var(--red); display:none; align-items:center; gap:9px;">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" flex-shrink="0" style="flex-shrink:0;">
            <circle cx="8" cy="8" r="7" stroke="#D63B3B" stroke-width="1.4"/>
            <path d="M8 5v4M8 11v.5" stroke="#D63B3B" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <span>Этот контрагент уже добавлен в ваш список. <a id="inn-duplicate-link" href="{{ route('cabinet.contractors') }}" style="color:var(--red);font-weight:600;text-decoration:underline;">Перейти к контрагентам</a></span>
        </div>
      </div>

      <div id="pane-manual" style="display:none;">
        <div class="field">
          <div class="field-label">ИНН</div>
          <input class="field-input" id="m-inn" type="text" placeholder="10 или 12 цифр" maxlength="12" autocomplete="off" inputmode="numeric">
        </div>
      </div>
    </div>
    <div class="form-divider"></div>

    <!-- 2. Реквизиты -->
    <div class="form-section disabled" id="sec-req">
      <div class="fs-head">
        <div class="fs-title"><div class="fs-num" id="s2-num">2</div>Реквизиты</div>
        <span class="auto-badge" id="req-auto-badge" style="display:none;">из ФНС</span>
      </div>
      <div class="field">
        <div class="field-label">Наименование</div>
        <input class="field-input" id="f-name" type="text" placeholder="ООО «Компания» / ИП Фамилия И. О.">
      </div>
      <div class="field-row-2">
        <div class="field">
          <div class="field-label">КПП <span class="opt">(для ООО/АО)</span></div>
          <input class="field-input" id="f-kpp" type="text" placeholder="—" maxlength="9">
        </div>
        <div class="field">
          <div class="field-label" id="f-ogrn-label">ОГРН</div>
          <input class="field-input" id="f-ogrn" type="text" placeholder="—">
        </div>
      </div>
      <div class="field">
        <div class="field-label">Юридический адрес</div>
        <input class="field-input" id="f-addr" type="text" placeholder="Город, улица, дом, офис">
      </div>
    </div>
    <div class="form-divider"></div>

    <!-- 3. Контакты -->
    <div class="form-section disabled" id="sec-contact">
      <div class="fs-head">
        <div class="fs-title"><div class="fs-num" id="s3-num">3</div>Контактные данные <span class="fs-hint">— необязательно</span></div>
      </div>
      <div class="field">
        <div class="field-label">Email <span class="opt">(для отправки документов)</span></div>
        <input class="field-input" id="f-email" type="email" placeholder="email@company.ru">
      </div>
      <div class="field-row-2">
        <div class="field">
          <div class="field-label">Телефон</div>
          <input class="field-input imask" id="f-phone" type="tel" placeholder="+7 ___ ___-__-__">
        </div>
        <div class="field">
          <div class="field-label">ФИО</div>
          <input class="field-input" id="f-contact" type="text" placeholder="Фамилия Имя Отчество">
        </div>
      </div>
    </div>
    <div class="form-divider"></div>

    <!-- 4. Банковские реквизиты -->
    <div class="form-section disabled" id="sec-bank">
      <div class="fs-head">
        <div class="fs-title"><div class="fs-num" id="s4-num">4</div>Банковские реквизиты <span class="fs-hint">— необязательно</span></div>
      </div>
      <div class="field">
        <div class="field-label">БИК банка</div>
        <div class="inn-wrap">
          <input class="field-input" id="f-bik" type="text" placeholder="9 цифр" maxlength="9" inputmode="numeric" style="padding-right:44px;">
          <div class="inn-status">
            <div class="spinner-ring" id="bik-spinner"></div>
            <svg id="bik-ok" width="20" height="20" viewBox="0 0 20 20" fill="none" style="display:none">
              <circle cx="10" cy="10" r="9" fill="#E6F7EF"/>
              <path d="M6 10l3 3 5-5" stroke="#159B6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <svg id="bik-err" width="20" height="20" viewBox="0 0 20 20" fill="none" style="display:none">
              <circle cx="10" cy="10" r="9" fill="#FEF0F0"/>
              <path d="M13 7l-6 6M7 7l6 6" stroke="#D63B3B" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
          </div>
        </div>
        <div class="inn-msg idle" id="bik-msg">Название банка подтянется автоматически</div>
      </div>
      <div class="field">
        <div class="field-label">Банк <span class="auto-badge" id="bank-auto-badge" style="display:none;">по БИК</span></div>
        <input class="field-input" id="f-bank" type="text" placeholder="Название банка">
      </div>
      <div class="field">
        <div class="field-label">Расчётный счёт</div>
        <input class="field-input" id="f-account" type="text" placeholder="40802 810 1 0000 0000000" maxlength="25" inputmode="numeric">
      </div>
    </div>

  </div><!-- /form-col -->

  <!-- ── Превью ── -->
  <div class="preview-col">
    <div class="preview-label">Карточка контрагента</div>

    <div class="cp-card" id="cp-card">
      <div class="cp-card-empty" id="card-empty">
        <svg width="56" height="56" viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="20" cy="20" r="10"/><circle cx="38" cy="20" r="10"/>
          <path d="M5 47c0-8 6.5-14 15-14h16c8.5 0 15 6 15 14"/>
        </svg>
        <div class="cp-card-empty-t">Карточка появится здесь</div>
        <div class="cp-card-empty-s">Введите ИНН — реквизиты заполнятся автоматически, и вы увидите готовую карточку контрагента</div>
      </div>

      <div id="card-filled" style="display:none;">
        <div class="cp-card-head">
          <div class="cp-card-ava" id="card-ava">—</div>
          <div>
            <div class="cp-card-name" id="card-name">—</div>
            <span class="cp-card-type" id="card-type">—</span>
          </div>
        </div>
        <div class="cp-card-body">
          <div class="cp-card-row"><div class="cp-card-k">ИНН</div><div class="cp-card-v" id="card-inn">—</div></div>
          <div class="cp-card-row" id="card-kpp-row"><div class="cp-card-k">КПП</div><div class="cp-card-v" id="card-kpp">—</div></div>
          <div class="cp-card-row"><div class="cp-card-k" id="card-ogrn-k">ОГРН</div><div class="cp-card-v" id="card-ogrn">—</div></div>
          <div class="cp-card-row"><div class="cp-card-k">Адрес</div><div class="cp-card-v" id="card-addr">—</div></div>
          <div class="cp-card-row"><div class="cp-card-k">Email</div><div class="cp-card-v empty" id="card-email">не указан</div></div>
          <div class="cp-card-row"><div class="cp-card-k">Телефон</div><div class="cp-card-v empty" id="card-phone">не указан</div></div>
          <div class="cp-card-row" id="card-bank-row" style="display:none;"><div class="cp-card-k">Счёт</div><div class="cp-card-v" id="card-account">—</div></div>
        </div>
      </div>
    </div>

    <div class="preview-foot">После сохранения контрагент станет доступен<br>для быстрого выбора при создании счетов и актов</div>
  </div>

</div><!-- /create-layout -->

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
const CSRF        = document.querySelector('meta[name="csrf-token"]').content;
const PARTY_URL   = '{{ route('dadata.party') }}';
const BANK_URL    = '{{ route('dadata.bank') }}';
const STORE_URL   = '{{ route('cabinet.contractors.store') }}';
const LIST_URL    = '{{ route('cabinet.contractors') }}';
const EXISTING_INNS = @json($existingInns);

const TYPE_COLOR = { 'ИП': '#159B6A', 'ООО': '#2550E2', 'АО': '#6B45D8' };

let innTimer = null;
let bikTimer = null;
let found    = false;
let mode     = 'auto';

function $q(id) { return document.getElementById(id); }

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

function detectType(inn) {
  if ((inn || '').length === 12) return 'ИП';
  if ((inn || '').length === 10) return 'ООО';
  return '';
}

/* ── INN auto lookup ── */
const innInput = $q('inn');
innInput.addEventListener('input', () => {
  const v = innInput.value.replace(/\D/g, '');
  innInput.value = v;
  innInput.classList.remove('valid', 'invalid');
  $q('inn-ok').style.display           = 'none';
  $q('inn-err').style.display          = 'none';
  $q('inn-duplicate-warn').style.display = 'none';
  found = false;
  clearTimeout(innTimer);
  $q('req-auto-badge').style.display = 'none';
  setMsg('idle', 'Реквизиты подтянутся автоматически из ФНС');
  if (v.length >= 10) {
    $q('inn-spinner').style.display = 'block';
    setMsg('idle', 'Запрашиваем данные в ФНС…');
    innTimer = setTimeout(() => doLookup(v), 700);
  } else {
    $q('inn-spinner').style.display = 'none';
    lockSections(true);
    syncCard();
  }
});

async function doLookup(inn) {
  $q('inn-spinner').style.display = 'none';
  try {
    const res  = await fetch(PARTY_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ inn }),
    });
    const json = await res.json();
    if (!res.ok) throw new Error();

    found = true;
    $q('inn-duplicate-warn').style.display = 'none';

    if (EXISTING_INNS.includes(inn)) {
      innInput.classList.add('invalid');
      $q('inn-err').style.display = 'block';
      setMsg('err', 'Контрагент с таким ИНН уже добавлен');
      $q('inn-duplicate-warn').style.display = 'flex';
      lockSections(true);
      found = false;
      syncCard();
      return;
    }

    innInput.classList.add('valid');
    $q('inn-ok').style.display = 'block';
    setMsg('ok', 'Найдено в ФНС — реквизиты заполнены');

    $q('f-name').value = json.short || json.name || '';
    $q('f-kpp').value  = json.kpp  || '';
    $q('f-ogrn').value = json.ogrn || '';
    $q('f-addr').value = json.address || '';
    $q('f-ogrn-label').textContent = inn.length === 12 ? 'ОГРНИП' : 'ОГРН';
    $q('req-auto-badge').style.display = '';

    const s1 = $q('s1-num');
    s1.classList.add('done');
    s1.innerHTML = '<svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 5l2.5 2.5 3.5-3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    lockSections(false);
  } catch {
    innInput.classList.add('invalid');
    $q('inn-err').style.display = 'block';
    setMsg('err', 'ИНН не найден в ФНС. Проверьте номер или введите данные вручную');
    const s1 = $q('s1-num');
    s1.classList.remove('done');
    s1.textContent = '1';
    lockSections(true);
  }
  syncCard();
}

function setMsg(kind, text) {
  const el = $q('inn-msg');
  el.className = 'inn-msg ' + kind;
  el.textContent = text;
}

function lockSections(locked) {
  ['sec-req', 'sec-contact', 'sec-bank'].forEach(id => {
    $q(id).classList.toggle('disabled', locked);
  });
}

/* ── Manual mode ── */
const mInn = $q('m-inn');
mInn.addEventListener('input', () => {
  mInn.value = mInn.value.replace(/\D/g, '');
  const t = detectType(mInn.value);
  if (t) $q('f-ogrn-label').textContent = t === 'ИП' ? 'ОГРНИП' : 'ОГРН';
  syncCard();
});

$q('mode-tabs').querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    mode = btn.dataset.mode;
    $q('mode-tabs').querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    $q('pane-auto').style.display   = mode === 'auto'   ? '' : 'none';
    $q('pane-manual').style.display = mode === 'manual' ? '' : 'none';
    if (mode === 'manual') {
      lockSections(false);
      $q('req-auto-badge').style.display = 'none';
      const s1 = $q('s1-num');
      s1.classList.remove('done');
      s1.textContent = '1';
    } else {
      lockSections(!found);
    }
    syncCard();

    const params = new URLSearchParams(location.search);
    if (mode === 'manual') {
      params.set('mode', 'manual');
    } else {
      params.delete('mode');
    }
    const qs = params.toString();
    history.replaceState(null, '', location.pathname + (qs ? '?' + qs : ''));
  });
});

/* ── BIK lookup ── */
function resetBikState() {
  $q('bik-spinner').style.display   = 'none';
  $q('bik-ok').style.display        = 'none';
  $q('bik-err').style.display       = 'none';
  $q('bank-auto-badge').style.display = 'none';
  $q('f-bik').classList.remove('valid', 'invalid');
}

function setBikMsg(kind, text) {
  const el = $q('bik-msg');
  el.className  = 'inn-msg ' + kind;
  el.textContent = text;
}

$q('f-bik').addEventListener('input', function() {
  this.value = this.value.replace(/\D/g, '');
  resetBikState();
  setBikMsg('idle', 'Название банка подтянется автоматически');
  clearTimeout(bikTimer);

  if (this.value.length > 0 && this.value.length < 9) {
    setBikMsg('idle', 'БИК содержит 9 цифр');
  }

  if (this.value.length === 9) {
    const bik = this.value;
    $q('bik-spinner').style.display = 'block';
    setBikMsg('idle', 'Ищем банк…');

    bikTimer = setTimeout(async () => {
      $q('bik-spinner').style.display = 'none';
      try {
        const res  = await fetch(BANK_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
          body: JSON.stringify({ bik }),
        });
        const json = await res.json();
        if (res.ok && json.name) {
          $q('f-bik').classList.add('valid');
          $q('bik-ok').style.display      = 'block';
          $q('f-bank').value              = json.name;
          $q('f-bank').readOnly           = true;
          $q('bank-auto-badge').style.display = '';
          setBikMsg('ok', 'Банк найден — название подставлено автоматически');
        } else {
          throw new Error();
        }
      } catch {
        $q('f-bik').classList.add('invalid');
        $q('bik-err').style.display = 'block';
        $q('f-bank').readOnly       = false;
        setBikMsg('err', 'Банк с таким БИК не найден. Введите название вручную');
      }
      syncCard();
    }, 500);
  } else {
    $q('f-bank').readOnly = false;
  }

  syncCard();
});

/* ── Live sync to preview card ── */
function currentInn() { return mode === 'auto' ? innInput.value : mInn.value; }

function syncCard() {
  const inn   = currentInn();
  const name  = $q('f-name').value.trim();
  const kpp   = $q('f-kpp').value.trim();
  const ogrn  = $q('f-ogrn').value.trim();
  const addr  = $q('f-addr').value.trim();
  const email = $q('f-email').value.trim();
  const phone = ($q('f-phone')._iMask?.value ?? $q('f-phone').value).trim();
  const acct  = $q('f-account').value.trim();
  const type  = detectType(inn);

  const hasInn  = inn.length >= 10;
  const hasData = hasInn && (found || mode === 'manual');

  if (!hasData || !name) {
    $q('card-empty').style.display  = '';
    $q('card-filled').style.display = 'none';
    $q('save-btn').disabled = true;
    return;
  }

  $q('card-empty').style.display  = 'none';
  $q('card-filled').style.display = '';

  const ava   = $q('card-ava');
  const color = TYPE_COLOR[type] || '#2550E2';
  ava.textContent  = initials(name);
  ava.style.background = color + '20';
  ava.style.color  = color;

  $q('card-name').textContent = name || 'Без названия';
  $q('card-type').textContent = type ? (type + (type === 'ИП' ? ' · физлицо' : ' · юрлицо')) : 'Контрагент';
  $q('card-inn').textContent  = inn;

  if (kpp) { $q('card-kpp-row').style.display = ''; $q('card-kpp').textContent = kpp; }
  else       $q('card-kpp-row').style.display = 'none';

  $q('card-ogrn-k').textContent = type === 'ИП' ? 'ОГРНИП' : 'ОГРН';
  setCardVal('card-ogrn',  ogrn);
  setCardVal('card-addr',  addr);
  setCardVal('card-email', email, 'не указан');
  setCardVal('card-phone', phone, 'не указан');

  if (acct) { $q('card-bank-row').style.display = ''; $q('card-account').textContent = acct; }
  else        $q('card-bank-row').style.display = 'none';

  $q('save-btn').disabled = !(name && inn.length >= 10);
}

function setCardVal(id, val, placeholder) {
  const el = $q(id);
  if (val) { el.textContent = val; el.classList.remove('empty'); }
  else      { el.textContent = placeholder || '—'; el.classList.add('empty'); }
}

['f-name','f-kpp','f-ogrn','f-addr','f-email','f-phone','f-contact','f-account','f-bank'].forEach(id => {
  $q(id).addEventListener('input', syncCard);
});

/* ── Save ── */
$q('save-btn').addEventListener('click', async function() {
  if (this.disabled) return;
  const origHtml = this.innerHTML;
  this.textContent = 'Сохраняем…';
  this.disabled = true;

  const inn = currentInn();
  const payload = {
    inn,
    name:            $q('f-name').value.trim(),
    full_name:       $q('f-name').value.trim(),
    kpp:             $q('f-kpp').value.trim()     || null,
    ogrn:            $q('f-ogrn').value.trim()    || null,
    address:         $q('f-addr').value.trim()    || null,
    email:           $q('f-email').value.trim()   || null,
    phone:           ($q('f-phone')._iMask?.unmaskedValue ?? $q('f-phone').value.replace(/\D/g, '')) || null,
    person_contract: $q('f-contact').value.trim() || null,
    payment_account: $q('f-account').value.trim() || null,
    bik:             $q('f-bik').value.trim()     || null,
    bank:            $q('f-bank').value.trim()    || null,
  };

  try {
    const res  = await fetch(STORE_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify(payload),
    });
    const json = await res.json();
    if (res.ok) {
      showToast('Контрагент «' + (payload.name || 'Без названия') + '» сохранён');
      setTimeout(() => { window.location.href = LIST_URL; }, 1100);
      return;
    }
    const first = json.errors ? Object.values(json.errors)[0] : null;
    showToast(Array.isArray(first) ? first[0] : (json.message ?? 'Ошибка сохранения'));
  } catch {
    showToast('Ошибка соединения');
  }
  this.innerHTML = origHtml;
  this.disabled  = false;
});

/* ── Toast ── */
let toastTimer;
function showToast(msg) {
  $q('toast-text').textContent = msg;
  const t = $q('toast');
  t.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => t.classList.remove('show'), 2500);
}

/* ── Init from URL params ── */
document.addEventListener('DOMContentLoaded', () => {

  lockSections(true);
  syncCard();

  const params = new URLSearchParams(location.search);

  if (params.get('mode') === 'manual') {
    const manualBtn = document.querySelector('.tab-btn[data-mode="manual"]');
    if (manualBtn) manualBtn.click();
  }

  const pInn = (params.get('inn') || '').replace(/\D/g, '');
  if (pInn.length >= 10) {
    innInput.value = pInn;
    innInput.dispatchEvent(new Event('input'));
  }
});
</script>
@endpush
