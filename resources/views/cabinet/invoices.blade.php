@extends('layouts.cabinet')

@section('title', 'Счета — СчётОк')

@section('content')
<div class="main-area docs-list">

  <header class="topbar">
    <span class="tb-title">Счета</span>
    <div class="tb-right">
      <a href="{{ route('cabinet.invoices.create') }}" class="btn btn-primary btn-sm">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
          <path d="M6.5 1.5v10M1.5 6.5h10"/>
        </svg>
        Создать счёт
      </a>
    </div>
  </header>

  <div class="filter-bar">
    <span class="filter-label">Контрагент:</span>
    <form method="GET" action="{{ route('cabinet.invoices') }}" style="display:contents;">
      <div class="filter-contractor">
        <x-form.mz-select name="contractor_id" onchange="this.form.submit()">
          <option value="">Все контрагенты</option>
          @foreach($contractors as $c)
            <option value="{{ $c->id }}" @selected($selectedContractorId === $c->id)>{{ $c->name }}</option>
          @endforeach
        </x-form.mz-select>
      </div>
    </form>
    @if($selectedContractorId)
      <a href="{{ route('cabinet.invoices') }}" class="filter-reset">Сбросить</a>
    @endif
    <span class="filter-info">Всего: {{ $invoices->total() }}</span>
    <x-cabinet.bulk-toolbar table-selector=".inv-table" :route="route('cabinet.invoices.bulk-delete')" />
  </div>

  <div class="content">
    @if($invoices->isEmpty())
      <div class="empty-state">
        <svg width="64" height="64" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M44 8H20a3 3 0 0 0-3 3v42a3 3 0 0 0 3 3h24a3 3 0 0 0 3-3V11a3 3 0 0 0-3-3z"/>
          <path d="M24 22h16M24 30h16M24 38h10"/>
        </svg>
        <div class="empty-state-title">Счетов пока нет</div>
        <div class="empty-state-sub">
          @if($selectedContractorId)
            У этого контрагента ещё нет счетов.
          @else
            Создайте первый счёт, нажав на кнопку «Создать счёт».
          @endif
        </div>
        @if($selectedContractorId)
          <a href="{{ route('cabinet.invoices') }}" class="btn btn-outline btn-sm" style="margin-top:4px;">Показать все счета</a>
        @else
          <a href="{{ route('cabinet.invoices.create') }}" class="btn btn-primary btn-sm" style="margin-top:4px;">Создать счёт</a>
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
              <th>Договор / основание</th>
              <th style="text-align:right;">Сумма</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($invoices as $inv)
              <tr>
                <td><input type="checkbox" class="bulk-select-row" value="{{ $inv->id }}"></td>
                <td class="td-num">
                  <a href="#" onclick="openInvoiceModal({{ $inv->id }});return false;">
                    № {{ $inv->number }}
                  </a>
                </td>
                <td class="td-date">{{ $inv->date?->format('d.m.Y') }}</td>
                <td class="td-cp">
                  <div class="td-cp-name" title="{{ $inv->contractor?->name }}">{{ $inv->contractor?->name ?? '—' }}</div>
                  <div class="td-cp-inn">ИНН {{ $inv->contractor?->inn ?? '—' }}</div>
                </td>
                <td class="td-basis">{{ $inv->contract?->name ?? $inv->basis ?? '—' }}</td>
                <td class="td-sum">{{ number_format($inv->total, 2, ',', ' ') }} ₽</td>
                <td class="td-actions">
                  <a href="{{ route('cabinet.invoices.pdf', $inv) }}" target="_blank" class="btn btn-sm btn-outline btn-pdf" title="Скачать PDF">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 1.5v6M3.5 5.5 6 8l2.5-2.5M1.5 10.5h9"/></svg>
                    PDF
                  </a>
                  <button class="btn btn-sm btn-danger-sm" onclick="deleteInvoice({{ $inv->id }})">Удалить</button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @if($invoices->hasPages())
        @php
          $last = $invoices->lastPage();
          $cur  = $invoices->currentPage();
          $pages = [];
          for ($i = 1; $i <= $last; $i++) {
            if ($i === 1 || $i === $last || abs($i - $cur) <= 2) {
              $pages[] = $i;
            }
          }
        @endphp
        <div class="pag-wrap">
          @if($invoices->onFirstPage())
            <span class="pag-btn pag-disabled">‹</span>
          @else
            <a href="{{ $invoices->previousPageUrl() }}" class="pag-btn">‹</a>
          @endif

          @php $prev = null; @endphp
          @foreach($pages as $page)
            @if($prev !== null && $page - $prev > 1)
              <span class="pag-ellipsis">…</span>
            @endif
            @if($page === $cur)
              <span class="pag-btn pag-active">{{ $page }}</span>
            @else
              <a href="{{ $invoices->url($page) }}" class="pag-btn">{{ $page }}</a>
            @endif
            @php $prev = $page; @endphp
          @endforeach

          @if($invoices->hasMorePages())
            <a href="{{ $invoices->nextPageUrl() }}" class="pag-btn">›</a>
          @else
            <span class="pag-btn pag-disabled">›</span>
          @endif
        </div>
      @endif
    @endif
  </div>

</div>

<x-cabinet.invoice-view-modal />

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

  async function deleteInvoice(id) {
    if (!confirm('Удалить счёт? Это действие нельзя отменить.')) return;
    try {
      const res = await fetch('/cabinet/invoices/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      });
      if (res.ok) {
        showToast('Счёт удалён');
        setTimeout(() => location.reload(), 600);
      } else {
        showToast('Не удалось удалить счёт');
      }
    } catch {
      showToast('Ошибка соединения');
    }
  }

</script>
@endpush
