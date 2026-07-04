@extends('layouts.cabinet')

@section('title', 'Акты — СчётОк')

@push('styles')
<style>
  .main { overflow: hidden; }
  .main-area { height: 100%; display: flex; flex-direction: column; overflow: hidden; }

  .topbar {
    height: 64px; flex-shrink: 0;
    background: var(--surface); border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 28px; gap: 16px;
  }
  .tb-title { font-size: 17px; font-weight: 700; color: var(--text-h); letter-spacing: -.3px; }
  .tb-right  { display: flex; align-items: center; gap: 8px; }

  .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: var(--rad-sm); font-family: inherit; font-size: 14px; font-weight: 500; cursor: pointer; border: none; text-decoration: none; transition: background .15s, transform .14s, box-shadow .14s; white-space: nowrap; }
  .btn-primary  { background: var(--accent); color: #fff; }
  .btn-primary:hover { background: var(--accent-hv); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37,80,226,.28); }
  .btn-outline  { background: transparent; color: var(--text-h); border: 1.5px solid var(--border); }
  .btn-outline:hover { background: var(--bg); }
  .btn-sm { padding: 7px 14px; font-size: 13px; }
  .btn-danger-sm { background: var(--red-lt); color: var(--red); border: 1.5px solid rgba(214,59,59,.2); }
  .btn-danger-sm:hover { background: #fde0e0; }

  /* Filter */
  .filter-bar {
    flex-shrink: 0;
    background: var(--surface); border-bottom: 1px solid var(--border);
    padding: 12px 28px;
    display: flex; align-items: center; gap: 12px;
  }
  .filter-label { font-size: 13px; font-weight: 600; color: var(--text-s); white-space: nowrap; }
  .filter-reset { font-size: 13px; color: var(--text-s); text-decoration: none; }
  .filter-reset:hover { color: var(--text-h); }
  .filter-info { margin-left: auto; font-size: 12.5px; color: var(--text-s); }

  /* Content */
  .content { flex: 1; overflow-y: auto; }

  /* Table */
  .table-wrap { padding: 20px 28px; }
  .inv-table { width: 100%; border-collapse: collapse; }
  .inv-table th {
    padding: 9px 14px; background: var(--bg); border-bottom: 2px solid var(--border);
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px;
    color: var(--text-s); text-align: left; white-space: nowrap;
  }
  .inv-table th:last-child { text-align: right; }
  .inv-table td {
    padding: 11px 14px; border-bottom: 1px solid var(--border);
    font-size: 13.5px; color: var(--text-h); vertical-align: middle;
  }
  .inv-table tbody tr:last-child td { border-bottom: none; }
  .inv-table tbody tr:nth-child(odd) td  { background: var(--surface); }
  .inv-table tbody tr:nth-child(even) td { background: var(--bg); }
  .inv-table tbody tr:hover td { background: var(--accent-lt); }

  .td-num a { font-weight: 700; color: var(--accent); text-decoration: none; }
  .td-num a:hover { text-decoration: underline; }
  .td-date { color: var(--text-s); white-space: nowrap; }
  .td-cp { max-width: 200px; }
  .td-cp-name { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; }
  .td-cp-inn  { font-size: 12px; color: var(--text-s); margin-top: 1px; }
  .td-basis { font-size: 12.5px; color: var(--text-s); max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .td-sum { font-weight: 600; white-space: nowrap; text-align: right; }
  .td-actions { text-align: right; white-space: nowrap; }
  .btn-pdf { color: var(--accent); border-color: rgba(37,80,226,.25); }
  .btn-pdf:hover { background: var(--accent-lt); border-color: var(--accent); }
  .modal-foot { padding: 0 24px 22px; display: flex; gap: 10px; justify-content: flex-end; }

  /* Empty */
  .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; padding: 80px 40px; text-align: center; color: var(--text-s); }
  .empty-state svg { opacity: .2; }
  .empty-state-title { font-size: 15px; font-weight: 600; color: var(--text-s); }
  .empty-state-sub { font-size: 13px; max-width: 300px; line-height: 1.6; }

  /* Pagination */
  .pag-wrap { display: flex; align-items: center; justify-content: center; gap: 4px; padding: 16px 28px 24px; }
  .pag-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 34px; height: 34px; padding: 0 10px;
    border-radius: var(--rad-sm); font-family: inherit; font-size: 13px; font-weight: 500;
    color: var(--text-h); background: var(--surface); border: 1.5px solid var(--border);
    text-decoration: none; cursor: pointer; transition: background .13s, border-color .13s;
  }
  .pag-btn:hover:not(.pag-active):not(.pag-disabled) { background: var(--bg); border-color: var(--text-s); }
  .pag-btn.pag-active { background: var(--accent); border-color: var(--accent); color: #fff; cursor: default; }
  .pag-btn.pag-disabled { opacity: .35; cursor: default; }
  .pag-ellipsis { padding: 0 4px; color: var(--text-s); font-size: 14px; line-height: 34px; }

  /* Modal */
  .modal-overlay { position: fixed; inset: 0; background: rgba(15,22,40,.5); z-index: 2000; display: none; align-items: center; justify-content: center; padding: 20px; }
  .modal-overlay.open { display: flex; }
  .modal { background: var(--surface); border-radius: 20px; box-shadow: 0 24px 64px rgba(15,22,40,.28); width: 100%; max-width: 480px; overflow: hidden; }
  .modal-head { padding: 22px 24px 16px; border-bottom: 1px solid var(--border); display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
  .modal-title { font-size: 17px; font-weight: 700; color: var(--text-h); letter-spacing: -.3px; }
  .modal-sub   { font-size: 13px; color: var(--text-s); margin-top: 4px; }
  .modal-x { width: 30px; height: 30px; border-radius: 8px; border: 1.5px solid var(--border); background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--text-s); font-size: 18px; transition: background .13s; flex-shrink: 0; }
  .modal-x:hover { background: var(--bg); }
  .modal-foot { padding: 0 24px 22px; display: flex; gap: 10px; justify-content: flex-end; }

  /* Toast */
  .toast { position: fixed; bottom: 28px; left: 50%; transform: translateX(-50%) translateY(20px); background: var(--dark); color: #fff; padding: 11px 20px; border-radius: 10px; font-size: 14px; font-weight: 500; opacity: 0; transition: opacity .25s, transform .25s; pointer-events: none; z-index: 9999; white-space: nowrap; display: flex; align-items: center; gap: 8px; }
  .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
  .toast svg { color: var(--green); }
</style>
@endpush

@section('content')
<div class="main-area">

  <header class="topbar">
    <span class="tb-title">Акты</span>
    <div class="tb-right">
      <a href="{{ route('cabinet.acts.create') }}" class="btn btn-primary btn-sm">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
          <path d="M6.5 1.5v10M1.5 6.5h10"/>
        </svg>
        Создать акт
      </a>
    </div>
  </header>

  <div class="filter-bar">
    <span class="filter-label">Контрагент:</span>
    <form method="GET" action="{{ route('cabinet.acts') }}" style="display:contents;">
      <div style="width:280px;">
        <x-form.mz-select name="contractor_id" onchange="this.form.submit()">
          <option value="">Все контрагенты</option>
          @foreach($contractors as $c)
            <option value="{{ $c->id }}" @selected($selectedContractorId === $c->id)>{{ $c->name }}</option>
          @endforeach
        </x-form.mz-select>
      </div>
    </form>
    @if($selectedContractorId)
      <a href="{{ route('cabinet.acts') }}" class="filter-reset">Сбросить</a>
    @endif
    <span class="filter-info">Всего: {{ $acts->total() }}</span>
    <x-cabinet.bulk-toolbar table-selector=".inv-table" :route="route('cabinet.acts.bulk-delete')" />
  </div>

  <div class="content">
    @if($acts->isEmpty())
      <div class="empty-state">
        <svg width="64" height="64" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M44 8H20a3 3 0 0 0-3 3v42a3 3 0 0 0 3 3h24a3 3 0 0 0 3-3V11a3 3 0 0 0-3-3z"/>
          <path d="M24 22h16M24 30h16M24 38h10"/>
          <path d="M22 46l5 5 10-10"/>
        </svg>
        <div class="empty-state-title">Актов пока нет</div>
        <div class="empty-state-sub">
          @if($selectedContractorId)
            У этого контрагента ещё нет актов.
          @else
            Создайте первый акт, нажав на кнопку «Создать акт».
          @endif
        </div>
        @if($selectedContractorId)
          <a href="{{ route('cabinet.acts') }}" class="btn btn-outline btn-sm" style="margin-top:4px;">Показать все акты</a>
        @else
          <a href="{{ route('cabinet.acts.create') }}" class="btn btn-primary btn-sm" style="margin-top:4px;">Создать акт</a>
        @endif
      </div>
    @else
      <div class="table-wrap">
        <table class="inv-table">
          <thead>
            <tr>
              <th style="width:36px;"><input type="checkbox" class="bulk-select-all" title="Выбрать всё"></th>
              <th>Номер</th>
              <th>Дата</th>
              <th>Контрагент</th>
              <th>Основание</th>
              <th style="text-align:right;">Сумма</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($acts as $act)
              <tr>
                <td><input type="checkbox" class="bulk-select-row" value="{{ $act->id }}"></td>
                <td class="td-num">
                  <a href="#" onclick="openActModal({{ $act->id }});return false;">
                    № {{ $act->number }}
                  </a>
                </td>
                <td class="td-date">{{ $act->date?->format('d.m.Y') }}</td>
                <td class="td-cp">
                  <div class="td-cp-name" title="{{ $act->contractor?->name }}">{{ $act->contractor?->name ?? '—' }}</div>
                  <div class="td-cp-inn">ИНН {{ $act->contractor?->inn ?? '—' }}</div>
                </td>
                <td class="td-basis">{{ $act->basis ?? '—' }}</td>
                <td class="td-sum">{{ number_format($act->total, 2, ',', ' ') }} ₽</td>
                <td class="td-actions">
                  <a href="{{ route('cabinet.acts.pdf', $act) }}" target="_blank" class="btn btn-sm btn-outline btn-pdf" title="Скачать PDF">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 1.5v6M3.5 5.5 6 8l2.5-2.5M1.5 10.5h9"/></svg>
                    PDF
                  </a>
                  <button class="btn btn-sm btn-danger-sm" onclick="deleteAct({{ $act->id }})">Удалить</button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @if($acts->hasPages())
        @php
          $last = $acts->lastPage();
          $cur  = $acts->currentPage();
          $pages = [];
          for ($i = 1; $i <= $last; $i++) {
            if ($i === 1 || $i === $last || abs($i - $cur) <= 2) {
              $pages[] = $i;
            }
          }
        @endphp
        <div class="pag-wrap">
          @if($acts->onFirstPage())
            <span class="pag-btn pag-disabled">‹</span>
          @else
            <a href="{{ $acts->previousPageUrl() }}" class="pag-btn">‹</a>
          @endif

          @php $prev = null; @endphp
          @foreach($pages as $page)
            @if($prev !== null && $page - $prev > 1)
              <span class="pag-ellipsis">…</span>
            @endif
            @if($page === $cur)
              <span class="pag-btn pag-active">{{ $page }}</span>
            @else
              <a href="{{ $acts->url($page) }}" class="pag-btn">{{ $page }}</a>
            @endif
            @php $prev = $page; @endphp
          @endforeach

          @if($acts->hasMorePages())
            <a href="{{ $acts->nextPageUrl() }}" class="pag-btn">›</a>
          @else
            <span class="pag-btn pag-disabled">›</span>
          @endif
        </div>
      @endif
    @endif
  </div>

</div>

<x-cabinet.act-view-modal />

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
  const CSRF = document.querySelector('meta[name="csrf-token"]').content;

  function escHtml(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function fmtMoney(n) {
    return new Intl.NumberFormat('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n) + ' ₽';
  }

  let toastTimer;
  function showToast(msg) {
    document.getElementById('toast-text').textContent = msg;
    const t = document.getElementById('toast');
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 2500);
  }

  async function deleteAct(id) {
    if (!confirm('Удалить акт? Это действие нельзя отменить.')) return;
    try {
      const res = await fetch('/cabinet/acts/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      });
      if (res.ok) {
        showToast('Акт удалён');
        setTimeout(() => location.reload(), 600);
      } else {
        showToast('Не удалось удалить акт');
      }
    } catch {
      showToast('Ошибка соединения');
    }
  }

</script>
@endpush
