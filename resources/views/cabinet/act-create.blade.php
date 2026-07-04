@extends('layouts.cabinet')

@section('title', 'Новый акт — СчётОк')

@push('styles')
<style>
  /* ── Topbar breadcrumb & actions ── */
  .tb-breadcrumb {
    display: flex; align-items: center; gap: 8px;
    font-size: 14px; color: var(--text-s);
  }
  .tb-breadcrumb a { color: var(--text-s); text-decoration: none; }
  .tb-breadcrumb a:hover { color: var(--text-h); }
  .tb-breadcrumb-sep { opacity: .4; }
  .tb-current { font-weight: 600; color: var(--text-h); }
  .topbar-actions {
    display: flex; align-items: center; gap: 0;
    border-left: 1px solid var(--border); height: 64px;
  }
  .tb-btn {
    display: flex; align-items: center; gap: 7px;
    padding: 0 22px; height: 64px;
    font-family: inherit; font-size: 14px; font-weight: 500;
    cursor: pointer; border: none; text-decoration: none;
    transition: background .14s; white-space: nowrap;
    border-left: 1px solid var(--border);
  }
  .tb-btn-ghost { background: transparent; color: var(--text-b); }
  .tb-btn-ghost:hover { background: var(--bg); }
  .tb-btn-primary { background: var(--accent); color: #fff; }
  .tb-btn-primary:hover { background: var(--accent-hv); }
  .tb-btn-primary:disabled { opacity: .5; cursor: not-allowed; }

  /* ── Create layout ── */
  .create-layout {
    flex: 1; display: grid;
    grid-template-columns: 580px 1fr;
    overflow: hidden;
  }
  .form-col {
    overflow-y: auto; border-right: 1px solid var(--border);
    background: var(--surface); padding: 24px 24px 40px;
    display: flex; flex-direction: column; gap: 0;
  }
  .preview-col {
    overflow-y: auto; background: var(--bg);
    padding: 28px 28px 40px;
    display: flex; flex-direction: column; align-items: center;
  }

  /* ── Form sections ── */
  .form-section { margin-bottom: 24px; }
  .fs-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 14px;
  }
  .fs-title {
    display: flex; align-items: center; gap: 8px;
    font-size: 14px; font-weight: 700; color: var(--text-h); letter-spacing: -.2px;
  }
  .fs-num {
    width: 22px; height: 22px; border-radius: 6px;
    background: var(--accent-lt); color: var(--accent);
    font-size: 11px; font-weight: 800;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .fs-num.done { background: var(--green-lt); color: var(--green); }
  .fs-action { font-size: 12.5px; color: var(--accent); text-decoration: none; cursor: pointer; }
  .fs-action:hover { text-decoration: underline; }
  .form-divider { height: 1px; background: var(--border); margin-bottom: 24px; }

  /* ── Requisites block ── */
  .req-block {
    background: var(--bg); border: 1px solid var(--border);
    border-radius: var(--rad); padding: 14px 16px;
  }
  .req-block-row {
    display: flex; align-items: flex-start; gap: 12px;
    font-size: 13px; padding: 5px 0;
    border-bottom: 1px solid var(--border);
  }
  .req-block-row:last-child { border-bottom: none; padding-bottom: 0; }
  .req-k { width: 68px; flex-shrink: 0; color: var(--text-s); font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; padding-top: 1px; }
  .req-v { color: var(--text-h); font-weight: 500; flex: 1; }

  /* ── Empty requisites notice ── */
  .req-empty {
    background: var(--bg); border: 1.5px dashed var(--border);
    border-radius: var(--rad); padding: 20px 16px;
    text-align: center;
  }
  .req-empty-title { font-size: 13.5px; font-weight: 700; color: var(--text-h); margin-bottom: 4px; }
  .req-empty-desc { font-size: 12.5px; color: var(--text-s); margin-bottom: 12px; }

  /* ── INN input ── */
  .inn-wrap { position: relative; margin-bottom: 10px; }
  .inn-input {
    width: 100%; padding: 12px 48px 12px 14px;
    border: 1.5px solid var(--border); border-radius: var(--rad-sm);
    font-family: inherit; font-size: 16px; font-weight: 600;
    color: var(--text-h); background: var(--bg); outline: none;
    letter-spacing: .3px;
    transition: border-color .18s, box-shadow .18s, background .18s;
  }
  .inn-input::placeholder { font-weight: 400; letter-spacing: 0; color: var(--text-s); }
  .inn-input:focus { border-color: var(--accent); background: var(--surface); box-shadow: 0 0 0 3px rgba(37,80,226,.12); }
  .inn-input.valid   { border-color: var(--green); background: var(--surface); }
  .inn-input.invalid { border-color: var(--red);   background: var(--red-lt); }
  .inn-status {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    width: 22px; height: 22px;
    display: flex; align-items: center; justify-content: center;
  }
  .spinner-ring {
    width: 18px; height: 18px;
    border: 2px solid var(--border); border-top-color: var(--accent);
    border-radius: 50%; animation: spin .7s linear infinite; display: none;
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  /* ── Autofill result ── */
  .autofill-result {
    background: var(--green-lt); border: 1px solid rgba(21,155,106,.2);
    border-radius: var(--rad-sm); padding: 12px 14px;
    opacity: 0; transform: translateY(4px);
    transition: opacity .3s ease, transform .3s ease;
    display: none;
  }
  .autofill-result.show { opacity: 1; transform: translateY(0); display: block; }
  .af-header { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
  .af-icon { color: var(--green); flex-shrink: 0; }
  .af-name { font-size: 14px; font-weight: 700; color: var(--text-h); }
  .af-fields { display: flex; flex-direction: column; gap: 3px; }
  .af-row { font-size: 12.5px; color: var(--text-b); }
  .af-row span { color: var(--text-s); }

  /* ── Fields ── */
  .field-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  .field { display: flex; flex-direction: column; gap: 5px; }
  .field-label { font-size: 12.5px; font-weight: 600; color: var(--text-s); text-transform: uppercase; letter-spacing: .5px; }
  .field-input {
    width: 100%; padding: 10px 13px;
    border: 1.5px solid var(--border); border-radius: var(--rad-sm);
    font-family: inherit; font-size: 14px; color: var(--text-h);
    background: var(--bg); outline: none;
    transition: border-color .16s, box-shadow .16s, background .16s;
  }
  .field-input:focus { border-color: var(--accent); background: var(--surface); box-shadow: 0 0 0 3px rgba(37,80,226,.10); }
  .field-select {
    width: 100%; padding: 10px 13px;
    border: 1.5px solid var(--border); border-radius: var(--rad-sm);
    font-family: inherit; font-size: 14px; color: var(--text-h);
    background: var(--bg); outline: none; cursor: pointer;
    transition: border-color .16s;
  }
  .field-select:focus { border-color: var(--accent); background: var(--surface); box-shadow: 0 0 0 3px rgba(37,80,226,.10); }

  /* ── Date ── */
  .date-wrap { position: relative; }
  .date-input {
    width: 100%; padding: 10px 13px 10px 38px;
    border: 1.5px solid var(--border); border-radius: var(--rad-sm);
    font-family: inherit; font-size: 14px; color: var(--text-h);
    background: var(--bg); outline: none; cursor: pointer;
    transition: border-color .16s, box-shadow .16s;
  }
  .date-input:focus, .date-input.open { border-color: var(--accent); background: var(--surface); box-shadow: 0 0 0 3px rgba(37,80,226,.10); }
  .date-ico {
    position: absolute; left: 11px; top: 50%;
    transform: translateY(-50%); pointer-events: none; color: var(--text-s);
  }
  .cal-pop {
    position: absolute; top: calc(100% + 6px); left: 0;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--rad); box-shadow: 0 8px 32px rgba(15,22,40,.14);
    z-index: 50; width: 272px; padding: 14px; display: none;
  }
  .cal-pop.open { display: block; }
  .cal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
  .cal-nav {
    width: 28px; height: 28px; border-radius: 7px;
    border: 1.5px solid var(--border); background: transparent;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    color: var(--text-s); font-size: 16px; transition: background .13s;
  }
  .cal-nav:hover { background: var(--bg); }
  .cal-month-lbl { font-size: 14px; font-weight: 700; color: var(--text-h); }
  .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; }
  .cal-dow { font-size: 10.5px; font-weight: 700; color: var(--text-s); text-align: center; padding: 4px 0; text-transform: uppercase; letter-spacing: .3px; }
  .cal-day {
    aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
    font-size: 13px; border-radius: 7px; cursor: pointer;
    color: var(--text-b); transition: background .12s, color .12s;
  }
  .cal-day:hover { background: var(--accent-lt); color: var(--accent); }
  .cal-day.empty { cursor: default; }
  .cal-day.empty:hover { background: transparent; }
  .cal-day.today { font-weight: 700; color: var(--accent); }
  .cal-day.selected { background: var(--accent); color: #fff; font-weight: 700; }
  .cal-day.selected:hover { background: var(--accent-hv); }

  /* ── Totals ── */
  .totals-block { background: var(--bg); border: 1px solid var(--border); border-radius: var(--rad); padding: 14px 16px; }
  .total-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 6px 0; font-size: 13.5px; border-bottom: 1px solid var(--border);
  }
  .total-row:last-child { border-bottom: none; padding-bottom: 0; }
  .total-row.final { padding-top: 12px; }
  .total-label { color: var(--text-b); }
  .total-value { font-weight: 600; color: var(--text-h); }
  .total-final { font-size: 17px; font-weight: 700; letter-spacing: -.3px; color: var(--text-h); }
  .vat-toggle { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-s); }
  .vat-select {
    border: 1.5px solid var(--border); border-radius: 6px;
    padding: 3px 6px; font-family: inherit; font-size: 13px;
    color: var(--text-h); background: var(--surface); outline: none; cursor: pointer;
  }

  /* ── Add item button ── */
  .add-item-btn {
    width: 100%; padding: 10px; border: none; border-top: 1px solid var(--border);
    background: transparent; font-family: inherit; font-size: 13px;
    color: var(--accent); font-weight: 600; cursor: pointer;
    transition: background .13s; display: flex; align-items: center;
    justify-content: center; gap: 6px;
  }
  .add-item-btn:hover { background: var(--accent-lt); }

  /* ── Tab group ── */
  .tab-group { display: flex; border: 1.5px solid var(--border); border-radius: var(--rad-sm); overflow: hidden; margin-bottom: 12px; }
  .tab-btn { flex: 1; padding: 8px; border: none; background: transparent; font-family: inherit; font-size: 13px; font-weight: 500; color: var(--text-s); cursor: pointer; transition: background .14s, color .14s; }
  .tab-btn.active { background: var(--accent); color: #fff; }
  .tab-btn:not(.active):hover { background: var(--bg); color: var(--text-h); }

  /* ── Auto number ── */
  .auto-num-row { display: flex; align-items: center; gap: 10px; padding: 10px 13px; border: 1.5px solid var(--border); border-radius: var(--rad-sm); background: var(--bg); }
  .auto-num-val { font-size: 15px; font-weight: 700; color: var(--text-h); }
  .auto-num-badge { font-size: 11px; font-weight: 600; color: var(--green); background: var(--green-lt); padding: 2px 8px; border-radius: 5px; }
  .auto-num-link { margin-left: auto; font-size: 12.5px; color: var(--accent); font-weight: 500; cursor: pointer; border: none; background: none; font-family: inherit; }
  .auto-num-link:hover { text-decoration: underline; }

  /* ── Item cards ── */
  .item-cards { display: flex; flex-direction: column; gap: 0; border: 1px solid var(--border); border-radius: var(--rad); overflow: hidden; }
  .item-card { padding: 14px 14px 12px; border-bottom: 1px solid var(--border); transition: background .12s; }
  .item-card:last-child { border-bottom: none; }
  .item-card:hover { background: var(--bg); }
  .item-name-row { display: flex; align-items: center; gap: 8px; margin-bottom: 9px; }
  .item-name-num { width: 18px; flex-shrink: 0; font-size: 11.5px; font-weight: 700; color: var(--text-s); text-align: center; }
  .item-name-input { flex: 1; padding: 9px 12px; border: 1.5px solid var(--border); border-radius: var(--rad-sm); font-family: inherit; font-size: 13.5px; color: var(--text-h); background: var(--surface); outline: none; transition: border-color .15s, box-shadow .15s; }
  .item-name-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37,80,226,.08); }
  .item-rm { width: 28px; height: 28px; border-radius: 6px; border: 1.5px solid var(--border); background: transparent; cursor: pointer; color: var(--text-s); font-size: 15px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: background .13s, color .13s, border-color .13s; }
  .item-rm:hover { background: var(--red-lt); color: var(--red); border-color: rgba(214,59,59,.3); }
  .item-params-row { display: grid; grid-template-columns: 80px 66px 1fr auto; gap: 8px; padding-left: 26px; align-items: end; }
  .item-param { display: flex; flex-direction: column; gap: 4px; }
  .item-param-label { font-size: 10.5px; font-weight: 700; color: var(--text-s); text-transform: uppercase; letter-spacing: .4px; }
  .item-param-input { width: 100%; padding: 8px 10px; border: 1.5px solid var(--border); border-radius: var(--rad-sm); font-family: inherit; font-size: 13px; color: var(--text-h); background: var(--surface); outline: none; transition: border-color .14s; }
  .item-param-input:focus { border-color: var(--accent); }
  .item-param-input.num { text-align: right; }
  .item-sum-col { display: flex; flex-direction: column; gap: 4px; }
  .item-sum-display { padding: 8px 2px; font-size: 13px; font-weight: 700; color: var(--text-h); text-align: right; white-space: nowrap; }

  /* ── Doc action buttons ── */
  .doc-action-btns { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
  .doc-action-btn { display: flex; align-items: center; gap: 10px; padding: 13px 14px; border: 1.5px solid var(--border); border-radius: var(--rad); background: var(--bg); font-family: inherit; font-size: 13px; font-weight: 500; color: var(--text-h); cursor: pointer; transition: background .14s, border-color .14s, box-shadow .14s, color .14s; text-align: left; }
  .doc-action-btn:hover { background: var(--surface); border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37,80,226,.08); color: var(--accent); }
  .doc-action-btn.applied { border-color: var(--green); color: var(--green); background: var(--green-lt); }
  .doc-action-btn.applied:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-lt); }
  .dab-ico { width: 34px; height: 34px; border-radius: 9px; background: var(--accent-lt); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .dab-ico svg { color: var(--accent); }
  .doc-action-btn.applied .dab-ico { background: var(--green-lt); }
  .doc-action-btn.applied .dab-ico svg { color: var(--green); }
  .doc-action-btn.applied:hover .dab-ico { background: var(--accent-lt); }
  .doc-action-btn.applied:hover .dab-ico svg { color: var(--accent); }
  .dab-title { font-size: 13px; font-weight: 600; line-height: 1.3; }
  .dab-sub { font-size: 11.5px; color: var(--text-s); margin-top: 1px; }
  .doc-action-btn.applied .dab-sub { color: var(--green); }
  .doc-action-btn.applied:hover .dab-sub { color: var(--accent); }

  /* ── Modals ── */
  .modal-overlay { position: fixed; inset: 0; background: rgba(15,22,40,.5); z-index: 2000; display: none; align-items: center; justify-content: center; padding: 20px; }
  .modal-overlay.open { display: flex; }
  .modal-head { padding: 22px 24px 0; display: flex; align-items: center; justify-content: space-between; }
  .modal-title { font-size: 16px; font-weight: 700; color: var(--text-h); letter-spacing: -.3px; }
  .modal-sub { font-size: 13px; color: var(--text-s); margin-top: 4px; }
  .modal-x { width: 30px; height: 30px; border-radius: 8px; border: 1.5px solid var(--border); background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--text-s); font-size: 18px; transition: background .13s; }
  .modal-x:hover { background: var(--bg); }
  .modal-body { padding: 20px 24px; }
  .modal-foot { padding: 0 24px 22px; display: flex; gap: 10px; justify-content: flex-end; }
  .modal-foot .btn { padding: 10px 22px; border-radius: var(--rad-sm); font-size: 14px; }
  .inv-sig-img { position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); max-height: 40px; max-width: 100%; }
  .inv-stamp-img { max-width: 80px; max-height: 80px; border-radius: 50%; object-fit: contain; }

  /* ── Act preview ── */
  .preview-label {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1px; color: var(--text-s); margin-bottom: 14px;
    display: flex; align-items: center; gap: 8px; width: 100%; max-width: 600px;
  }
  .preview-label::after { content: ''; flex: 1; height: 1px; background: var(--border); }
  .invoice-doc {
    background: #fff; width: 100%; max-width: 600px;
    border: 1px solid #aaa;
    box-shadow: 0 4px 24px rgba(15,22,40,.09);
    padding: 28px 32px 32px;
    font-family: 'Golos Text', sans-serif;
    font-size: 12px; color: #111; flex-shrink: 0;
  }
  .inv-placeholder { color: #bbb; font-style: italic; }

  /* Title */
  .inv-title { font-size: 16px; font-weight: 700; color: #000; margin-bottom: 6px; }
  .inv-divider { height: 2px; background: #000; margin-bottom: 14px; }

  /* Parties */
  .inv-party-row { display: flex; gap: 6px; margin-bottom: 9px; font-size: 11.5px; line-height: 1.5; }
  .inv-pk { width: 80px; flex-shrink: 0; color: #222; font-weight: 600; }
  .inv-pv { flex: 1; }

  .inv-basis-row { font-size: 11.5px; margin-bottom: 14px; }
  .inv-bk { color: #222; margin-right: 6px; }
  .inv-bv { font-weight: 700; }

  /* Act table */
  .inv-table { width: 100%; border-collapse: collapse; margin: 14px 0 0; font-size: 11px; }
  .inv-table th { background: #f0f2f7; padding: 6px 8px; border: 1px solid #888; color: #222; font-weight: 700; text-align: center; font-size: 11px; vertical-align: middle; }
  .inv-table td { padding: 6px 8px; border: 1px solid #888; color: #111; vertical-align: top; }
  .inv-table td.tc { text-align: center; }
  .inv-table td.tr { text-align: right; }
  .inv-table td.empty-row { color: #bbb; font-style: italic; text-align: center; padding: 14px; }

  /* Totals */
  .inv-totals-wrap { display: flex; justify-content: flex-end; margin-top: 6px; }
  .inv-totals { font-size: 12px; border-collapse: collapse; }
  .inv-total-row td { padding: 3px 0; white-space: nowrap; vertical-align: baseline; }
  .inv-total-row td:first-child { padding-right: 24px; }
  .inv-total-row td:last-child { text-align: right; font-weight: 600; }
  .inv-total-row.inv-grand-total td { font-weight: 700; font-size: 12.5px; border-top: 1.5px solid #000; padding-top: 6px; }

  /* Summary */
  .inv-summary { margin-top: 14px; font-size: 11.5px; }
  .inv-count { margin-bottom: 3px; }
  .inv-words { font-weight: 700; font-size: 12px; }

  /* Completion text */
  .act-completion { margin-top: 14px; font-size: 11px; color: #333; line-height: 1.65; }

  /* Signature */
  .act-sig-area { margin-top: 24px; border-top: 1.5px solid #000; padding-top: 14px; }
  .act-sig-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
  .act-sig-head { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px; }
  .act-sig-name { font-size: 11px; color: #333; line-height: 1.5; min-height: 32px; }
  .act-sig-line { border-bottom: 1px solid #000; height: 40px; position: relative; margin-top: 8px; }
  .act-sig-abbr { text-align: center; font-size: 10.5px; color: #333; margin-top: 4px; }
  .act-stamp { margin-top: 8px; }
  .inv-stamp-circle {
    width: 80px; height: 80px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
  }
</style>
@endpush

@section('content')

<header class="topbar" style="padding: 0 0 0 28px;">
  <div class="tb-left">
    <div class="tb-breadcrumb">
      <a href="{{ route('cabinet') }}">Кабинет</a>
      <span class="tb-breadcrumb-sep">›</span>
      <span class="tb-current">Новый акт</span>
    </div>
  </div>
  <div class="topbar-actions">
    <button class="tb-btn tb-btn-primary" id="create-pdf-btn">
      <svg width="15" height="15" viewBox="0 0 15 15" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M8.5 2H4a1.5 1.5 0 0 0-1.5 1.5v8A1.5 1.5 0 0 0 4 13h7a1.5 1.5 0 0 0 1.5-1.5V6.5M8.5 2v4.5H13M7.5 8v3.5M6 10h3"/>
      </svg>
      Сформировать PDF
    </button>
  </div>
</header>

<div class="create-layout">

  <!-- ── FORM ── -->
  <div class="form-col">

    <!-- 1. Ваши реквизиты -->
    <div class="form-section">
      <div class="fs-head">
        <div class="fs-title">
          @if($reqData)
          <div class="fs-num done">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 5l2.5 2.5 3.5-3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          @else
          <div class="fs-num">1</div>
          @endif
          Ваши реквизиты
        </div>
        <a class="fs-action" href="{{ route('cabinet.settings') }}#tab-org">Изменить</a>
      </div>

      @if($reqData)
      <div class="req-block">
        <div class="req-block-row">
          <span class="req-k">ИНН</span>
          <span class="req-v">{{ $reqData['inn'] }}</span>
        </div>
        @if($reqData['name'])
        <div class="req-block-row">
          <span class="req-k">Статус</span>
          <span class="req-v">{{ $reqData['name'] }}</span>
        </div>
        @endif
        @if($reqData['ogrn'])
        <div class="req-block-row">
          <span class="req-k">{{ $reqData['ogrn_label'] }}</span>
          <span class="req-v">{{ $reqData['ogrn'] }}</span>
        </div>
        @endif
        @if($reqData['address'])
        <div class="req-block-row">
          <span class="req-k">Адрес</span>
          <span class="req-v">{{ $reqData['address'] }}</span>
        </div>
        @endif
        @if($bank)
        <div class="req-block-row">
          <span class="req-k">Р/счёт</span>
          <span class="req-v">{{ $bank->payment_account }}{{ $bank->bank ? ' · ' . $bank->bank : '' }}</span>
        </div>
        @endif
      </div>
      @else
      <div class="req-empty">
        <div class="req-empty-title">Реквизиты не заполнены</div>
        <div class="req-empty-desc">Добавьте данные организации — они автоматически подставятся в акт.</div>
        <a href="{{ route('cabinet.settings') }}" class="btn btn-primary" style="font-size:13px;padding:9px 18px;">Заполнить реквизиты</a>
      </div>
      @endif
    </div>
    <div class="form-divider"></div>

    <!-- 2. Банковский счёт -->
    <div class="form-section">
      <div class="fs-head">
        <div class="fs-title">
          @if($bank)
          <div class="fs-num done">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 5l2.5 2.5 3.5-3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          @else
          <div class="fs-num">2</div>
          @endif
          Банковский счёт
        </div>
        <a class="fs-action" href="{{ route('cabinet.settings') }}#tab-bank">Изменить</a>
      </div>

      @if($banks->isEmpty())
      <div class="req-empty">
        <div class="req-empty-title">Расчётный счёт не добавлен</div>
        <div class="req-empty-desc">Добавьте банковский счёт — он будет указан в акте.</div>
        <a href="{{ route('cabinet.settings') }}" class="btn btn-primary" style="font-size:13px;padding:9px 18px;">Добавить счёт</a>
      </div>
      @else
      @if($banks->count() > 1)
      <div class="field" style="margin-bottom:10px;">
        <x-form.mz-select id="bank-select">
          @foreach($banks as $b)
          <option value="{{ $b->id }}"
            data-bank="{{ $b->bank }}"
            data-city="{{ $b->city }}"
            data-bik="{{ $b->bik }}"
            data-corr="{{ $b->correspondent_account }}"
            data-payment="{{ $b->payment_account }}"
            {{ $loop->first ? 'selected' : '' }}>
            {{ $b->payment_account }}{{ $b->bank ? ' · ' . $b->bank : '' }}{{ $b->is_primary ? ' (основной)' : '' }}
          </option>
          @endforeach
        </x-form.mz-select>
      </div>
      @endif
      <div class="req-block" id="bank-details">
        <div class="req-block-row">
          <span class="req-k">Банк</span>
          <span class="req-v" id="bd-bank">{{ $bank->bank ?? '—' }}</span>
        </div>
        @if($bank->city)
        <div class="req-block-row" id="bd-city-row">
          <span class="req-k">Город</span>
          <span class="req-v" id="bd-city">{{ $bank->city }}</span>
        </div>
        @else
        <div class="req-block-row" id="bd-city-row" style="display:none">
          <span class="req-k">Город</span>
          <span class="req-v" id="bd-city"></span>
        </div>
        @endif
        <div class="req-block-row">
          <span class="req-k">БИК</span>
          <span class="req-v" id="bd-bik">{{ $bank->bik ?? '—' }}</span>
        </div>
        @if($bank->correspondent_account)
        <div class="req-block-row" id="bd-corr-row">
          <span class="req-k">К/счёт</span>
          <span class="req-v" id="bd-corr">{{ $bank->correspondent_account }}</span>
        </div>
        @else
        <div class="req-block-row" id="bd-corr-row" style="display:none">
          <span class="req-k">К/счёт</span>
          <span class="req-v" id="bd-corr"></span>
        </div>
        @endif
        <div class="req-block-row">
          <span class="req-k">Р/счёт</span>
          <span class="req-v" id="bd-payment">{{ $bank->payment_account ?? '—' }}</span>
        </div>
      </div>
      @endif
    </div>
    <div class="form-divider"></div>

    <!-- 3. Заказчик -->
    <div class="form-section">
      <div class="fs-head">
        <div class="fs-title"><div class="fs-num" id="step3-num">3</div>Заказчик</div>
      </div>
      <div class="tab-group" id="cp-tab-group">
        <button class="tab-btn active" data-target="pane-cp-list">Выбрать из списка</button>
        <button class="tab-btn" data-target="pane-cp-inn">Ввести ИНН</button>
      </div>
      <!-- Ввод ИНН -->
      <div id="pane-cp-inn" style="display:none">
        <div class="field" style="margin-bottom:10px;">
          <div class="field-label">ИНН заказчика</div>
          <div class="inn-wrap">
            <input class="inn-input" id="cp-inn" type="text" placeholder="Введите ИНН (10 или 12 цифр)" maxlength="12" autocomplete="off">
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
        </div>
        <div class="autofill-result" id="cp-result">
          <div class="af-header">
            <svg class="af-icon" width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="6" fill="#159B6A"/><path d="M4.5 7l2 2 3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <div class="af-name" id="cp-name">—</div>
          </div>
          <div class="af-fields">
            <div class="af-row"><span>ИНН: </span><span id="cp-inn-show">—</span></div>
            <div class="af-row" id="cp-kpp-row" style="display:none"><span>КПП: </span><span id="cp-kpp">—</span></div>
            <div class="af-row"><span id="cp-org-type">ОГРН: </span><span id="cp-ogrn">—</span></div>
            <div class="af-row"><span>Адрес: </span><span id="cp-addr">—</span></div>
          </div>
        </div>
      </div>
      <!-- Выбор из списка -->
      <div id="pane-cp-list">
        <div class="field">
          <div class="field-label">Контрагент</div>
          <x-form.mz-select id="cp-select" placeholder="— выберите контрагента —">
            @foreach($contractors as $c)
            <option value="{{ $c['id'] }}">{{ $c['name'] }}{{ $c['inn'] ? ' · ' . $c['inn'] : '' }}</option>
            @endforeach
          </x-form.mz-select>
        </div>
        <div class="autofill-result" id="cp-result-list" style="margin-top:10px;"></div>
      </div>
    </div>
    <div class="form-divider"></div>

    <!-- 4. Номер и дата -->
    <div class="form-section">
      <div class="fs-head">
        <div class="fs-title"><div class="fs-num" id="step4-num">4</div>Номер, дата и основание</div>
      </div>
      <div class="field-row-2" style="margin-bottom:14px;">
        <div class="field">
          <div class="field-label">Номер акта</div>
          <div class="auto-num-row" id="num-auto-row">
            <span class="auto-num-val" id="num-display">1</span>
            <span class="auto-num-badge">авто</span>
            <button class="auto-num-link" id="num-manual-btn">Ввести вручную</button>
          </div>
          <input class="field-input" id="act-num" type="text" value="1" style="display:none;">
        </div>
        <div class="field">
          <div class="field-label">Дата</div>
          <div class="date-wrap">
            <svg class="date-ico" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
              <rect x="1.5" y="2.5" width="13" height="12" rx="2"/><path d="M5 1.5v2M11 1.5v2M1.5 6.5h13"/>
            </svg>
            <input class="date-input" id="act-date" type="text" readonly>
            <div class="cal-pop" id="calendar">
              <div class="cal-header">
                <button class="cal-nav" id="cal-prev">‹</button>
                <span class="cal-month-lbl" id="cal-label"></span>
                <button class="cal-nav" id="cal-next">›</button>
              </div>
              <div class="cal-grid" id="cal-grid"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="field">
        <div class="field-label" style="margin-bottom:6px;">
          Основание
          <span style="color:var(--text-s);font-weight:400;text-transform:none;letter-spacing:0;font-size:11.5px;">(необязательно)</span>
        </div>
        <div class="tab-group" id="basis-tab-group">
          <button class="tab-btn active" data-target="pane-basis-list">Выбрать договор</button>
          <button class="tab-btn" data-target="pane-basis-manual">Ввести вручную</button>
        </div>
        <div id="pane-basis-manual" style="display:none">
          <input class="field-input" id="act-basis" type="text" placeholder="Договор № 26-1-1 от 01.01.2026">
        </div>
        <div id="pane-basis-list">
          <x-form.mz-select id="basis-select" placeholder="Без договора" />
        </div>
      </div>
    </div>
    <div class="form-divider"></div>

    <!-- 5. Позиции -->
    <div class="form-section">
      <div class="fs-head">
        <div class="fs-title"><div class="fs-num" id="step5-num">5</div>Позиции акта</div>
      </div>
      <div class="item-cards" id="items-body"></div>
      <button class="add-item-btn" id="add-item">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M6.5 1.5v10M1.5 6.5h10"/></svg>
        Добавить позицию
      </button>
    </div>
    <div class="form-divider"></div>

    <!-- 6. Итого -->
    <div class="form-section">
      <div class="fs-head">
        <div class="fs-title"><div class="fs-num" id="step6-num">6</div>Итоговая сумма</div>
        <div class="vat-toggle">
          НДС:
          <x-form.mz-select id="vat-select" :compact="true"
            :options="['0' => 'Без НДС', '20' => '20%', '10' => '10%']" />
        </div>
      </div>
      <div class="totals-block">
        <div class="total-row">
          <span class="total-label">Итого без НДС</span>
          <span class="total-value" id="total-sub">0,00 ₽</span>
        </div>
        <div class="total-row" id="vat-row" style="display:none">
          <span class="total-label" id="vat-label">НДС 20%</span>
          <span class="total-value" id="total-vat">0,00 ₽</span>
        </div>
        <div class="total-row final">
          <span class="total-label" style="font-weight:700;color:var(--text-h);">Всего к оплате</span>
          <span class="total-final" id="total-final">0,00 ₽</span>
        </div>
      </div>
    </div>

    <!-- 7. Подпись и печать -->
    <div class="form-section">
      <div class="fs-head">
        <div class="fs-title"><div class="fs-num" id="step7-num">7</div>Подпись и печать</div>
      </div>
      <div class="doc-action-btns">
        <button class="doc-action-btn" id="open-sig-modal">
          <div class="dab-ico">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 13.5c2-2 3-3.5 4-3.5s1.5 1.5 3 1.5 2.5-2 4-5"/>
              <path d="M3 15h12"/>
            </svg>
          </div>
          <div>
            <div class="dab-title">Поставить подпись</div>
            <div class="dab-sub" id="sig-sub">Загрузите изображение</div>
          </div>
        </button>
        <button class="doc-action-btn" id="open-stamp-modal">
          <div class="dab-ico">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="9" cy="9" r="7"/>
              <circle cx="9" cy="9" r="3.5"/>
              <path d="M9 2v2M9 14v2M2 9h2M14 9h2"/>
            </svg>
          </div>
          <div>
            <div class="dab-title">Поставить печать</div>
            <div class="dab-sub" id="stamp-sub">Загрузите изображение</div>
          </div>
        </button>
      </div>
    </div>

    <div style="padding: 8px 0 4px; display:flex; gap:8px;">
      <button class="tb-btn tb-btn-primary" id="save-act-btn" style="flex:1;height:48px;border-left:none;border-radius:var(--rad-sm);font-size:15px;justify-content:center;">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M13.5 10.5v2a1 1 0 0 1-1 1h-9a1 1 0 0 1-1-1v-2M8 2v8M5 7l3 3 3-3"/>
        </svg>
        Сохранить акт
      </button>
    </div>

  </div><!-- /form-col -->

  <!-- ── PREVIEW ── -->
  <div class="preview-col">
    <div class="preview-label">Предпросмотр акта</div>
    <div class="invoice-doc" id="act-preview">

      <!-- Заголовок -->
      <div class="inv-title">Акт № <span id="pv-num">1</span> от <span id="pv-date">—</span></div>
      <div class="inv-divider"></div>

      <!-- Стороны -->
      <div class="inv-party-row">
        <div class="inv-pk">Исполнитель:</div>
        <div class="inv-pv" id="pv-executor">
          @if($reqData)
            <strong>{{ $reqData['name'] }}, ИНН {{ $reqData['inn'] }}</strong>
            @if($bank), р/с {{ $bank->payment_account }}
              @if($bank->bank), в банке {{ $bank->bank }}@endif
              @if($bank->bik), БИК {{ $bank->bik }}@endif
              @if($bank->correspondent_account), к/с {{ $bank->correspondent_account }}@endif
            @endif
          @else
            <span class="inv-placeholder">Заполните реквизиты</span>
          @endif
        </div>
      </div>
      <div class="inv-party-row">
        <div class="inv-pk">Заказчик:</div>
        <div class="inv-pv" id="pv-cp-name"><span class="inv-placeholder">Введите ИНН контрагента</span></div>
      </div>
      <div class="inv-basis-row">
        <span class="inv-bk">Основание:</span><span class="inv-bv" id="pv-basis">&nbsp;</span>
      </div>

      <!-- Таблица позиций -->
      <table class="inv-table">
        <thead>
          <tr>
            <th style="width:26px;">№</th>
            <th>Наименование работ, услуг</th>
            <th style="width:44px;">Кол-во</th>
            <th style="width:30px;">Ед.</th>
            <th style="width:72px;">Цена</th>
            <th style="width:72px;">Сумма</th>
          </tr>
        </thead>
        <tbody id="pv-items">
          <tr><td colspan="6" class="empty-row">Добавьте позиции акта</td></tr>
        </tbody>
      </table>

      <!-- Итого -->
      <div class="inv-totals-wrap">
        <table class="inv-totals">
          <tr class="inv-total-row"><td>Итого:</td><td id="pv-sub">0,00</td></tr>
          <tr class="inv-total-row" id="pv-vat-row" style="display:none"><td id="pv-vat-label">НДС 20%:</td><td id="pv-vat">0,00</td></tr>
          <tr class="inv-total-row" id="pv-novat-row"><td>Без налога (НДС)</td><td>-</td></tr>
        </table>
      </div>

      <!-- Сводка -->
      <div class="inv-summary">
        <div class="inv-count" id="pv-count">Всего оказано услуг 0, на сумму 0,00 руб.</div>
        <div class="inv-words" id="pv-words">Ноль рублей 00 копеек</div>
      </div>

      <!-- Текст о выполнении -->
      <div class="act-completion">
        Вышеперечисленные услуги выполнены полностью и в срок. Заказчик претензий по объему, качеству и срокам оказания услуг не имеет.
      </div>

      <!-- Подпись -->
      <div class="act-sig-area">
        <div class="act-sig-cols">
          <div>
            <div class="act-sig-head">ИСПОЛНИТЕЛЬ</div>
            <div class="act-sig-name">
              @if($reqData && $reqData['ogrn_label'] !== 'ОГРНИП')
                Генеральный директор
              @endif
              {{ $reqData['name'] ?? '' }}
            </div>
            <div class="act-sig-line" id="pv-sig-exec-line"></div>
            <div class="act-sig-abbr" id="pv-exec-abbr"></div>
            <div class="act-stamp">
              <div class="inv-stamp-circle" id="pv-stamp-exec"></div>
            </div>
          </div>
          <div>
            <div class="act-sig-head">ЗАКАЗЧИК</div>
            <div class="act-sig-name" id="pv-zakazchik-name"><span class="inv-placeholder">—</span></div>
            <div class="act-sig-line"></div>
          </div>
        </div>
      </div>

    </div>
  </div>

</div><!-- /create-layout -->

<x-cabinet.upload-modal
  prefix="sig"
  title="Поставить подпись"
  subtitle="Загрузите PNG или JPG с изображением подписи"
  drop-text="Нажмите или перетащите файл подписи"
  hint-text="PNG, JPG, SVG · до 5 МБ"
  alt="Подпись"
>
  <svg width="32" height="32" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-s);display:block;margin:0 auto;">
    <path d="M16 4v16M8 12l8-8 8 8M6 26h20"/>
  </svg>
</x-cabinet.upload-modal>

<x-cabinet.upload-modal
  prefix="stamp"
  title="Поставить печать"
  subtitle="Загрузите PNG или JPG с изображением печати"
  drop-text="Нажмите или перетащите файл печати"
  hint-text="PNG, JPG, SVG · до 5 МБ · лучше с прозрачным фоном"
  alt="Печать"
  preview-style="border-radius:50%;object-fit:contain;"
>
  <svg width="32" height="32" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" style="color:var(--text-s);display:block;margin:0 auto;">
    <circle cx="16" cy="16" r="12"/><circle cx="16" cy="16" r="5"/>
    <path d="M16 4v4M16 24v4M4 16h4M24 16h4"/>
  </svg>
</x-cabinet.upload-modal>

@endsection

@push('scripts')
<script>
(function () {
  const CONTRACTORS = @json($contractors);
  const DADATA_URL  = '{{ route('dadata.party') }}';
  const CSRF        = document.querySelector('meta[name="csrf-token"]').content;

  /* ── State ── */
  let cpData = null;
  let innTimer = null;
  let items = [{ name: '', unit: 'шт.', qty: 1, price: 0 }];
  let selectedDate = new Date();
  let calViewing   = new Date();
  let selectedContractorId = null;
  let selectedContractId   = null;
  let selectedBankAccountId = {{ $bank?->id ?? 'null' }};
  const PRESELECTED_CONTRACTOR_ID = {{ $preselectedContractorId ?? 'null' }};

  /* ── INN lookup ── */
  const cpInn    = document.getElementById('cp-inn');
  const spinner  = document.getElementById('inn-spinner');
  const okIcon   = document.getElementById('inn-ok');
  const errIcon  = document.getElementById('inn-err');
  const cpResult = document.getElementById('cp-result');

  cpInn.addEventListener('input', () => {
    const v = cpInn.value.replace(/\D/g, '');
    cpInn.value = v;
    cpInn.classList.remove('valid', 'invalid');
    okIcon.style.display = 'none';
    errIcon.style.display = 'none';
    cpResult.classList.remove('show');
    cpData = null;
    selectedContractorId = null;
    selectedContractId   = null;
    clearTimeout(innTimer);
    refreshBasisContracts([]);
    updatePreview();
    if (v.length >= 10) {
      spinner.style.display = 'block';
      innTimer = setTimeout(() => doLookup(v), 700);
    }
  });

  async function doLookup(inn) {
    try {
      const res = await fetch(DADATA_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ inn }),
      });
      spinner.style.display = 'none';
      if (!res.ok) throw new Error();
      const data = await res.json();
      if (data.error) throw new Error();

      const isIP = !data.kpp;
      cpData = { name: data.name, kpp: data.kpp || null, ogrn: data.ogrn, addr: data.address };
      cpInn.classList.add('valid');
      okIcon.style.display = 'block';
      document.getElementById('cp-name').textContent     = data.name;
      document.getElementById('cp-inn-show').textContent = inn;
      if (data.kpp) {
        document.getElementById('cp-kpp-row').style.display = '';
        document.getElementById('cp-kpp').textContent = data.kpp;
      } else {
        document.getElementById('cp-kpp-row').style.display = 'none';
      }
      document.getElementById('cp-org-type').textContent = isIP ? 'ОГРНИП: ' : 'ОГРН: ';
      document.getElementById('cp-ogrn').textContent     = data.ogrn;
      document.getElementById('cp-addr').textContent     = data.address;
      cpResult.classList.add('show');
      const matchedCp = CONTRACTORS.find(x => x.inn === inn);
      selectedContractorId = matchedCp?.id ?? null;
      fetchBasisContracts(selectedContractorId);
      if (selectedContractorId) {
        fetchNextNumber();
      } else if (document.getElementById('num-auto-row').style.display !== 'none') {
        document.getElementById('num-display').textContent = 1;
        document.getElementById('act-num').value = 1;
        updatePreview();
      }
    } catch (e) {
      spinner.style.display = 'none';
      cpInn.classList.add('invalid');
      errIcon.style.display = 'block';
      cpData = null;
      selectedContractorId = null;
      refreshBasisContracts([]);
    }
    updatePreview();
  }

  /* ── Contractor select from list ── */
  const cpSelect     = document.getElementById('cp-select');
  const cpResultList = document.getElementById('cp-result-list');

  cpSelect.addEventListener('change', () => {
    const id = +cpSelect.value;
    if (!id) {
      cpData = null; selectedContractorId = null;
      cpResultList.classList.remove('show'); cpResultList.innerHTML = '';
      refreshBasisContracts([]); updatePreview(); return;
    }
    const c = CONTRACTORS.find(x => x.id === id);
    if (!c) return;
    const isIP = !c.kpp;
    cpData = { name: c.name, kpp: c.kpp || null, ogrn: c.ogrn, addr: c.address };
    cpResultList.innerHTML = `
      <div class="af-header">
        <svg class="af-icon" width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="6" fill="#159B6A"/><path d="M4.5 7l2 2 3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <div class="af-name">${escHtml(c.name)}</div>
      </div>
      <div class="af-fields">
        <div class="af-row"><span>ИНН: </span><span>${escHtml(c.inn || '')}</span></div>
        ${c.kpp ? `<div class="af-row"><span>КПП: </span><span>${escHtml(c.kpp)}</span></div>` : ''}
        ${c.ogrn ? `<div class="af-row"><span>${isIP ? 'ОГРНИП: ' : 'ОГРН: '}</span><span>${escHtml(c.ogrn)}</span></div>` : ''}
        ${c.address ? `<div class="af-row"><span>Адрес: </span><span>${escHtml(c.address)}</span></div>` : ''}
      </div>`;
    cpResultList.classList.add('show');
    selectedContractorId = id;
    fetchBasisContracts(id);
    fetchNextNumber();
    updatePreview();
  });

  /* ── Calendar ── */
  const MONTHS = ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
  const DAYS   = ['Пн','Вт','Ср','Чт','Пт','Сб','Вс'];
  const calPop  = document.getElementById('calendar');
  const dateInp = document.getElementById('act-date');

  function formatDate(d) {
    return d.toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' });
  }

  function renderCal() {
    const y = calViewing.getFullYear(), m = calViewing.getMonth();
    document.getElementById('cal-label').textContent = MONTHS[m] + ' ' + y;
    let g = DAYS.map(d => `<div class="cal-dow">${d}</div>`).join('');
    let fd = new Date(y, m, 1).getDay(); if (fd === 0) fd = 7; fd--;
    for (let i = 0; i < fd; i++) g += '<div class="cal-day empty"></div>';
    const tot = new Date(y, m + 1, 0).getDate();
    for (let d = 1; d <= tot; d++) {
      const date = new Date(y, m, d);
      const sel  = selectedDate && date.toDateString() === selectedDate.toDateString();
      const tod  = date.toDateString() === new Date().toDateString();
      g += `<div class="cal-day${sel ? ' selected' : ''}${tod && !sel ? ' today' : ''}" data-y="${y}" data-m="${m}" data-d="${d}">${d}</div>`;
    }
    document.getElementById('cal-grid').innerHTML = g;
    document.getElementById('cal-grid').querySelectorAll('.cal-day:not(.empty)').forEach(el => {
      el.addEventListener('click', () => {
        selectedDate = new Date(+el.dataset.y, +el.dataset.m, +el.dataset.d);
        dateInp.value = formatDate(selectedDate);
        calPop.classList.remove('open'); dateInp.classList.remove('open');
        renderCal(); updatePreview();
      });
    });
  }

  dateInp.addEventListener('click', e => { e.stopPropagation(); calPop.classList.toggle('open'); dateInp.classList.toggle('open'); renderCal(); });
  document.getElementById('cal-prev').addEventListener('click', e => { e.stopPropagation(); calViewing.setMonth(calViewing.getMonth() - 1); renderCal(); });
  document.getElementById('cal-next').addEventListener('click', e => { e.stopPropagation(); calViewing.setMonth(calViewing.getMonth() + 1); renderCal(); });
  document.addEventListener('click', () => { calPop.classList.remove('open'); dateInp.classList.remove('open'); });

  selectedDate = new Date();
  dateInp.value = formatDate(selectedDate);
  renderCal();

  /* ── Line items ── */
  function escHtml(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;'); }

  function renderItems() {
    const body = document.getElementById('items-body');
    body.innerHTML = items.map((it, i) => `
      <div class="item-card">
        <div class="item-name-row">
          <span class="item-name-num">${i + 1}</span>
          <input class="item-name-input" data-i="${i}" data-f="name" type="text"
            placeholder="Наименование работы или услуги" value="${escHtml(it.name)}">
          <button class="item-rm" data-i="${i}" title="Удалить">×</button>
        </div>
        <div class="item-params-row">
          <div class="item-param">
            <div class="item-param-label">Кол-во</div>
            <input class="item-param-input num" data-i="${i}" data-f="qty" type="number" min="1" step="any" value="${it.qty}">
          </div>
          <div class="item-param">
            <div class="item-param-label">Ед.</div>
            <input class="item-param-input item-unit-input" data-i="${i}" data-f="unit" type="text" value="${escHtml(it.unit)}">
          </div>
          <div class="item-param">
            <div class="item-param-label">Цена, ₽</div>
            <input class="item-param-input num" data-i="${i}" data-f="price" type="number" min="0" step="any" value="${it.price || ''}">
          </div>
          <div class="item-sum-col">
            <div class="item-param-label">Сумма</div>
            <div class="item-sum-display">${fmtMoneyPlain(it.qty * it.price)} ₽</div>
          </div>
        </div>
      </div>
    `).join('');
    body.querySelectorAll('[data-f]').forEach(inp => {
      inp.addEventListener('input', () => {
        if (inp.dataset.f === 'qty') {
          const v = Math.max(1, +inp.value || 1);
          items[+inp.dataset.i].qty = v;
          inp.value = v;
        } else {
          items[+inp.dataset.i][inp.dataset.f] = inp.type === 'number' ? +inp.value : inp.value;
        }
        const card = inp.closest('.item-card');
        const idx  = +inp.dataset.i;
        const sumEl = card.querySelector('.item-sum-display');
        if (sumEl) sumEl.textContent = fmtMoneyPlain(items[idx].qty * items[idx].price) + ' ₽';
        updateTotals(); renderPreviewItems();
      });
    });
    body.querySelectorAll('.item-rm').forEach(btn => {
      btn.addEventListener('click', () => {
        if (items.length > 1) { items.splice(+btn.dataset.i, 1); renderItems(); updateTotals(); renderPreviewItems(); }
      });
    });
  }

  document.getElementById('add-item').addEventListener('click', () => {
    items.push({ name: '', unit: 'шт.', qty: 1, price: 0 }); renderItems(); updateTotals(); renderPreviewItems();
  });

  /* ── Totals ── */
  function fmtMoney(n) {
    return n.toLocaleString('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ₽';
  }
  function fmtMoneyPlain(n) {
    return n.toLocaleString('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function updateTotals() {
    const sub    = items.reduce((s, it) => s + it.qty * it.price, 0);
    const vat    = +document.getElementById('vat-select').value;
    const vatAmt = sub * vat / 100;
    const total  = sub + vatAmt;

    document.getElementById('total-sub').textContent   = fmtMoney(sub);
    document.getElementById('total-vat').textContent   = fmtMoney(vatAmt);
    document.getElementById('total-final').textContent = fmtMoney(total);
    document.getElementById('vat-row').style.display   = vat > 0 ? '' : 'none';

    document.getElementById('pv-sub').textContent   = fmtMoneyPlain(sub);
    document.getElementById('pv-vat').textContent   = fmtMoneyPlain(vatAmt);
    document.getElementById('pv-vat-row').style.display   = vat > 0 ? '' : 'none';
    document.getElementById('pv-novat-row').style.display = vat > 0 ? 'none' : '';
    document.getElementById('pv-vat-label').textContent   = 'НДС ' + vat + '%:';

    const cnt = items.filter(it => it.name || it.price).length;
    document.getElementById('pv-count').textContent =
      'Всего оказано услуг ' + cnt + ', на сумму ' + fmtMoneyPlain(total) + ' руб.';
    document.getElementById('pv-words').textContent = numToWords(Math.round(total));
  }

  document.getElementById('vat-select').addEventListener('change', updateTotals);

  /* ── Preview ── */
  function updatePreview() {
    document.getElementById('pv-num').textContent  = document.getElementById('act-num').value;
    document.getElementById('pv-date').textContent = selectedDate ? formatDate(selectedDate) : '—';

    const basis = document.getElementById('act-basis').value.trim();
    document.getElementById('pv-basis').textContent = basis || ' ';

    if (cpData) {
      let cpStr = cpData.name + ', ИНН ' + (cpInn.value || '');
      if (cpData.kpp)  cpStr += ', КПП ' + cpData.kpp;
      if (cpData.addr) cpStr += ', ' + cpData.addr;
      document.getElementById('pv-cp-name').textContent    = cpStr;
      document.getElementById('pv-zakazchik-name').textContent = cpData.name;
    } else {
      document.getElementById('pv-cp-name').innerHTML         = '<span class="inv-placeholder">Введите ИНН контрагента</span>';
      document.getElementById('pv-zakazchik-name').innerHTML  = '<span class="inv-placeholder">—</span>';
    }
  }

  function renderPreviewItems() {
    if (!items.length) {
      document.getElementById('pv-items').innerHTML =
        '<tr><td colspan="6" class="empty-row">Добавьте позиции акта</td></tr>';
      updateTotals(); return;
    }
    document.getElementById('pv-items').innerHTML = items.map((it, i) => `
      <tr>
        <td class="tc">${i + 1}</td>
        <td>${escHtml(it.name) || '<span style="color:#bbb;font-style:italic;">—</span>'}</td>
        <td class="tc">${it.qty}</td>
        <td class="tc">${escHtml(it.unit)}</td>
        <td class="tr">${fmtMoneyPlain(it.price)}</td>
        <td class="tr">${fmtMoneyPlain(it.qty * it.price)}</td>
      </tr>
    `).join('');
    updateTotals();
  }

  /* ── Number in words ── */
  function numToWords(n) {
    if (n === 0) return 'Ноль рублей 00 копеек';
    const ones    = ['','один','два','три','четыре','пять','шесть','семь','восемь','девять'];
    const teens   = ['десять','одиннадцать','двенадцать','тринадцать','четырнадцать','пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать'];
    const tens    = ['','','двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят','восемьдесят','девяносто'];
    const hundreds = ['','сто','двести','триста','четыреста','пятьсот','шестьсот','семьсот','восемьсот','девятьсот'];
    function threeDigits(n, fem = false) {
      let r = '', h = Math.floor(n / 100), t = n % 100, u = n % 10;
      if (h) r += hundreds[h] + ' ';
      if (t >= 10 && t <= 19) r += teens[t - 10] + ' ';
      else {
        if (Math.floor(t / 10)) r += tens[Math.floor(t / 10)] + ' ';
        if (u) r += (fem && u === 1 ? 'одна' : fem && u === 2 ? 'две' : ones[u]) + ' ';
      }
      return r.trim();
    }
    function plural(n, f1, f2, f5) {
      const m = Math.abs(n) % 100, u = m % 10;
      if (m > 10 && m < 20) return f5;
      if (u === 1) return f1;
      if (u >= 2 && u <= 4) return f2;
      return f5;
    }
    let result = '';
    const mil = Math.floor(n / 1000000), th = Math.floor((n % 1000000) / 1000), hun = n % 1000;
    if (mil) result += threeDigits(mil) + ' ' + plural(mil, 'миллион', 'миллиона', 'миллионов') + ' ';
    if (th)  result += threeDigits(th, true) + ' ' + plural(th, 'тысяча', 'тысячи', 'тысяч') + ' ';
    if (hun) result += threeDigits(hun) + ' ';
    result = result.trim();
    result = result.charAt(0).toUpperCase() + result.slice(1);
    return result + ' ' + plural(n, 'рубль', 'рубля', 'рублей') + ' 00 копеек';
  }

  /* ── Bank account selector ── */
  const bankSelect = document.getElementById('bank-select');
  if (bankSelect) {
    bankSelect.addEventListener('change', () => {
      selectedBankAccountId = +bankSelect.value || null;
      applyBankSelection();
    });
  }

  function applyBankSelection() {
    if (!bankSelect) return;
    const opt = bankSelect.options[bankSelect.selectedIndex];
    if (!opt) return;
    document.getElementById('bd-bank').textContent    = opt.dataset.bank    || '—';
    document.getElementById('bd-bik').textContent     = opt.dataset.bik     || '—';
    document.getElementById('bd-payment').textContent = opt.dataset.payment  || '—';
    const cityRow = document.getElementById('bd-city-row');
    const bdCity  = document.getElementById('bd-city');
    if (opt.dataset.city) {
      if (bdCity)  bdCity.textContent    = opt.dataset.city;
      if (cityRow) cityRow.style.display = '';
    } else {
      if (cityRow) cityRow.style.display = 'none';
    }
    const corrRow = document.getElementById('bd-corr-row');
    const bdCorr  = document.getElementById('bd-corr');
    if (opt.dataset.corr) {
      if (bdCorr)  bdCorr.textContent    = opt.dataset.corr;
      if (corrRow) corrRow.style.display = '';
    } else {
      if (corrRow) corrRow.style.display = 'none';
    }
  }

  /* ── Basis contracts ── */
  function refreshBasisContracts(list, autoSelectFirst = false) {
    const sel  = document.getElementById('basis-select');
    const root = sel?.closest('[data-mz-select]');
    if (!root) return;
    const dropdown = root.querySelector('.mz-select__dropdown');
    const trigger  = root.querySelector('.mz-select__trigger');

    while (sel.options.length > 1) sel.remove(1);
    list.forEach(ct => {
      const label = ct.name + (ct.number ? ' № ' + ct.number : '');
      const [y, m, d] = (ct.date || '').split('-');
      const dateStr = d ? `${d}.${m}.${y}` : '';
      const value = dateStr ? `${label} от ${dateStr}` : label;
      const opt = new Option(label, value);
      opt.dataset.contractId = ct.id;
      sel.add(opt);
    });

    const selectIdx = (autoSelectFirst && sel.options.length > 1) ? 1 : 0;
    sel.selectedIndex = selectIdx;

    if (dropdown) {
      dropdown.innerHTML = '';
      Array.from(sel.options).forEach((option, idx) => {
        const li = document.createElement('li');
        li.className = 'mz-select__option' + (option.disabled ? ' is-disabled' : '');
        li.textContent = option.textContent;
        li.dataset.value = option.value;
        li.dataset.index = String(idx);
        li.setAttribute('role', 'option');
        li.setAttribute('aria-selected', idx === selectIdx ? 'true' : 'false');
        if (option.disabled) li.setAttribute('aria-disabled', 'true');
        li.addEventListener('click', () => {
          if (option.disabled) return;
          sel.selectedIndex = idx;
          sel.dispatchEvent(new Event('change', { bubbles: true }));
          root.classList.remove('is-open');
          if (trigger) trigger.setAttribute('aria-expanded', 'false');
          trigger?.focus();
        });
        dropdown.appendChild(li);
      });
    }

    if (trigger) {
      const opt = sel.options[selectIdx];
      trigger.textContent = opt ? opt.textContent : '';
      if (selectIdx > 0) {
        root.classList.add('has-value');
        root.classList.remove('is-open');
      } else {
        root.classList.remove('has-value', 'is-open');
      }
    }
    const chosenOpt = sel.options[selectIdx];
    document.getElementById('act-basis').value = selectIdx > 0 ? chosenOpt.value : '';
    selectedContractId = selectIdx > 0 ? (+chosenOpt.dataset.contractId || null) : null;
    updatePreview();
  }

  async function fetchBasisContracts(contractorId, autoSelectFirst = false) {
    if (!contractorId) { refreshBasisContracts([]); return; }
    try {
      const res = await fetch('/cabinet/contractors/' + contractorId + '/contracts', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      });
      refreshBasisContracts(res.ok ? await res.json() : [], autoSelectFirst);
    } catch {
      refreshBasisContracts([]);
    }
  }

  /* ── Init ── */
  renderItems();
  updateTotals();
  updatePreview();
  renderPreviewItems();

  if (PRESELECTED_CONTRACTOR_ID) {
    const c = CONTRACTORS.find(x => x.id === PRESELECTED_CONTRACTOR_ID);
    if (c) {
      const isIP = !c.kpp;
      cpData = { name: c.name, kpp: c.kpp || null, ogrn: c.ogrn, addr: c.address };
      cpResultList.innerHTML = `
        <div class="af-header">
          <svg class="af-icon" width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="6" fill="#159B6A"/><path d="M4.5 7l2 2 3-3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          <div class="af-name">${escHtml(c.name)}</div>
        </div>
        <div class="af-fields">
          <div class="af-row"><span>ИНН: </span><span>${escHtml(c.inn || '')}</span></div>
          ${c.kpp ? `<div class="af-row"><span>КПП: </span><span>${escHtml(c.kpp)}</span></div>` : ''}
          ${c.ogrn ? `<div class="af-row"><span>${isIP ? 'ОГРНИП: ' : 'ОГРН: '}</span><span>${escHtml(c.ogrn)}</span></div>` : ''}
          ${c.address ? `<div class="af-row"><span>Адрес: </span><span>${escHtml(c.address)}</span></div>` : ''}
        </div>`;
      cpResultList.classList.add('show');
      selectedContractorId = PRESELECTED_CONTRACTOR_ID;
      cpSelect.value = String(PRESELECTED_CONTRACTOR_ID);
      const cpRoot = cpSelect.closest('[data-mz-select]');
      if (cpRoot) {
        const cpTrigger = cpRoot.querySelector('.mz-select__trigger');
        if (cpTrigger && cpSelect.selectedIndex >= 0) {
          cpTrigger.textContent = cpSelect.options[cpSelect.selectedIndex].textContent;
          cpRoot.classList.add('has-value');
        }
      }
      fetchBasisContracts(PRESELECTED_CONTRACTOR_ID, true);
      fetchNextNumber();
      updatePreview();
    }
  }

  document.getElementById('act-num').addEventListener('input', updatePreview);
  document.getElementById('act-basis').addEventListener('input', updatePreview);

  /* ── Tab groups ── */
  function initTabs(groupId, resetCpData = false) {
    const group = document.getElementById(groupId);
    if (!group) return;
    group.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        group.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        group.querySelectorAll('.tab-btn').forEach(b => {
          const el = document.getElementById(b.dataset.target);
          if (el) el.style.display = 'none';
        });
        const show = document.getElementById(btn.dataset.target);
        if (show) show.style.display = '';
        if (resetCpData) cpData = null;
        updatePreview();
      });
    });
  }
  initTabs('cp-tab-group', true);
  initTabs('basis-tab-group');
  document.querySelectorAll('#cp-tab-group .tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      if (btn.dataset.target === 'pane-cp-list') {
        selectedContractorId = +document.getElementById('cp-select').value || null;
      } else {
        const inn = document.getElementById('cp-inn').value;
        selectedContractorId = CONTRACTORS.find(x => x.inn === inn)?.id ?? null;
      }
      fetchBasisContracts(selectedContractorId);
      fetchNextNumber();
    });
  });

  /* ── Basis select ── */
  document.getElementById('basis-select').addEventListener('change', function () {
    document.getElementById('act-basis').value = this.value || '';
    const opt = this.options[this.selectedIndex];
    selectedContractId = opt?.dataset.contractId ? +opt.dataset.contractId : null;
    updatePreview();
  });

  /* ── Auto number ── */
  async function fetchNextNumber() {
    if (document.getElementById('num-auto-row').style.display === 'none') return;
    try {
      const url = new URL('{{ route('cabinet.acts.next-number') }}', location.origin);
      if (selectedContractorId) url.searchParams.set('contractor_id', selectedContractorId);
      const res = await fetch(url, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      });
      if (!res.ok) return;
      const { number } = await res.json();
      document.getElementById('num-display').textContent = number;
      document.getElementById('act-num').value = number;
      updatePreview();
    } catch {}
  }

  document.getElementById('num-manual-btn').addEventListener('click', () => {
    document.getElementById('num-auto-row').style.display = 'none';
    const inp = document.getElementById('act-num');
    inp.style.display = '';
    inp.focus();
  });

  /* ── Modals ── */
  function openModal(id) { document.getElementById(id).classList.add('open'); }
  function closeModal(id) { document.getElementById(id).classList.remove('open'); }
  // openModal/closeModal используются также из отдельного <script> компонента
  // x-cabinet.upload-modal (модалка подписи/печати) — без этого они не видны
  // за пределами текущего IIFE.
  window.openModal = openModal;
  window.closeModal = closeModal;

  document.querySelectorAll('[data-close]').forEach(btn => {
    btn.addEventListener('click', () => closeModal(btn.dataset.close));
  });

  /* ── File upload ── */
  setupUpload('sig-zone', 'sig-file', 'sig-preview-wrap', 'sig-preview-img', 'sig-re', 'sig-apply', 'open-sig-modal', (src, scale) => {
    const lineEl = document.getElementById('pv-sig-exec-line');
    if (lineEl) {
      const existing = document.getElementById('pv-sig-img');
      if (existing) existing.remove();
      const sigImg = document.createElement('img');
      sigImg.id = 'pv-sig-img'; sigImg.src = src; sigImg.className = 'inv-sig-img';
      sigImg.style.maxHeight = Math.round(40 * scale / 100) + 'px';
      lineEl.appendChild(sigImg);
    }
    document.getElementById('sig-sub').textContent = 'Подпись добавлена';
  });

  setupUpload('stamp-zone', 'stamp-file', 'stamp-preview-wrap', 'stamp-preview-img', 'stamp-re', 'stamp-apply', 'open-stamp-modal', (src, scale) => {
    const circle = document.getElementById('pv-stamp-exec');
    if (circle) {
      const size = Math.round(80 * scale / 100) + 'px';
      circle.style.width  = size;
      circle.style.height = size;
      circle.innerHTML = '<img class="inv-stamp-img" src="' + src + '" alt="Печать" style="max-width:' + size + ';max-height:' + size + ';">';
    }
    document.getElementById('stamp-sub').textContent = 'Печать добавлена';
  });

  /* ── PDF placeholder ── */
  document.getElementById('create-pdf-btn').addEventListener('click', () => {
    alert('Формирование PDF будет доступно в следующей версии.');
  });

  /* ── Save act ── */
  document.getElementById('save-act-btn').addEventListener('click', async function () {
    if (!selectedContractorId && !cpData) {
      alert('Выберите заказчика');
      return;
    }
    const validItems = items.filter(it => it.name.trim() || it.price > 0);
    if (!validItems.length) {
      alert('Добавьте хотя бы одну позицию');
      return;
    }

    const origHtml = this.innerHTML;
    this.textContent = 'Сохраняем…';
    this.disabled = true;

    const d = selectedDate;
    const dateStr = d
      ? `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`
      : null;

    const payload = {
      ...(selectedContractorId
        ? { contractor_id: selectedContractorId }
        : { new_contractor: { inn: cpInn.value, name: cpData.name, kpp: cpData.kpp || null, ogrn: cpData.ogrn || null, address: cpData.addr || null } }),
      contract_id:     selectedContractId,
      bank_account_id: selectedBankAccountId,
      number:          document.getElementById('act-num').value.trim(),
      date:            dateStr,
      basis:           document.getElementById('act-basis').value.trim() || null,
      nds_rate:        +document.getElementById('vat-select').value,
      items:           validItems.map(it => ({
        name:  it.name,
        unit:  it.unit,
        qty:   it.qty,
        price: it.price,
      })),
    };

    try {
      const res  = await fetch('{{ route('cabinet.acts.store') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(payload),
      });
      const json = await res.json();
      if (res.ok) {
        window.location.href = json.redirect ?? '{{ route('cabinet') }}';
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
