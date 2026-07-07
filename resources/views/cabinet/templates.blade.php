@extends('layouts.cabinet')

@section('title', 'Шаблоны — СчётОк')

@section('content')
<div class="main-area templates-page">

  <header class="topbar">
    <div>
      <div class="tb-title">Шаблоны</div>
      <div class="tb-sub">Счета, формируемые автоматически по расписанию</div>
    </div>
  </header>

  <div class="content">
    @if($templates->isEmpty())
      <div class="empty-state">
        <svg width="64" height="64" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M44 8H20a3 3 0 0 0-3 3v42a3 3 0 0 0 3 3h24a3 3 0 0 0 3-3V11a3 3 0 0 0-3-3z"/>
          <path d="M24 22h16M24 30h16M24 38h10"/>
          <circle cx="38" cy="42" r="8"/>
          <path d="M38 39v3l2 2"/>
        </svg>
        <div class="empty-state-title">Шаблонов пока нет</div>
        <div class="empty-state-sub">
          Создайте счёт с функцией «Умный счёт» — он автоматически станет шаблоном и будет формироваться по расписанию.
        </div>
        <a href="{{ route('cabinet.invoices.create') }}" class="btn btn-primary btn-sm" style="margin-top:4px;">Создать умный счёт</a>
      </div>
    @else
      <div class="tpl-grid">
        @foreach($templates as $tpl)
          @php
            $t     = $tpl->invoiceTemplate;
            $label = match((int)$tpl->period_months) {
              1 => 'Каждый месяц',
              2 => 'Каждые 2 месяца',
              3 => 'Каждые 3 месяца',
              6 => 'Каждые 6 месяцев',
              default => 'Каждые ' . $tpl->period_months . ' мес.',
            };
            $total = collect($t->items)->sum(fn($it) => $it['qty'] * $it['price']);
            $nds   = round($total * $t->nds_rate / 100, 2);
          @endphp
          <div class="tpl-card {{ $tpl->is_active ? '' : 'inactive' }}" onclick="openTplModal({{ $tpl->id }})">
            <div class="tpl-card-head">
              <div>
                <div class="tpl-contractor">{{ $t->contractor?->name ?? '—' }}</div>
                <div class="tpl-inn">ИНН {{ $t->contractor?->inn ?? '—' }}</div>
              </div>
              <span class="tpl-badge {{ $tpl->is_active ? 'tpl-badge-active' : 'tpl-badge-inactive' }}">
                {{ $tpl->is_active ? 'Активен' : 'Пауза' }}
              </span>
            </div>

            <div class="tpl-meta">
              <div class="tpl-meta-item">
                <span class="tpl-meta-label">Периодичность</span>
                <span class="tpl-meta-value">{{ $label }}</span>
              </div>
              <div class="tpl-meta-item">
                <span class="tpl-meta-label">День месяца</span>
                <span class="tpl-meta-value">{{ $tpl->day_of_month }} число</span>
              </div>
              <div class="tpl-meta-item">
                <span class="tpl-meta-label">Сумма</span>
                <span class="tpl-meta-value">{{ number_format($total + $nds, 2, ',', ' ') }} ₽</span>
              </div>
              <div class="tpl-meta-item">
                <span class="tpl-meta-label">НДС</span>
                <span class="tpl-meta-value">{{ $t->nds_rate > 0 ? $t->nds_rate . '%' : 'Без НДС' }}</span>
              </div>
            </div>

            <div class="tpl-tags">
              <span class="tpl-tag">{{ $label }}</span>
              @if($tpl->with_act)
                <span class="tpl-tag tpl-tag-act">+ Акт</span>
              @endif
              @if($t->basis)
                <span class="tpl-tag" style="background:var(--bg);color:var(--text-s);border:1px solid var(--border);">{{ Str::limit($t->basis, 30) }}</span>
              @endif
            </div>

            <div class="tpl-footer" onclick="event.stopPropagation()">
              <div>
                <div class="tpl-next-label">Следующий запуск</div>
                <div class="tpl-next-date">{{ $tpl->next_run_at ? $tpl->next_run_at->format('d.m.Y') : '—' }}</div>
              </div>
              <div class="tpl-actions">
                <button class="tpl-act-btn {{ $tpl->is_active ? 'pause' : 'resume' }}"
                        onclick="toggleTemplate({{ $tpl->id }}, this)">
                  {{ $tpl->is_active ? 'Пауза' : 'Возобновить' }}
                </button>
                <button class="tpl-del-btn" onclick="deleteTemplate({{ $tpl->id }}, this)" title="Удалить">×</button>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

</div>

<!-- Модальное окно: детали шаблона -->
<div class="modal-overlay" id="tpl-modal">
  <div class="modal">
    <div class="modal-head">
      <div>
        <div class="modal-title" id="tpl-modal-title">Шаблон</div>
        <div class="modal-sub" id="tpl-modal-sub"></div>
      </div>
      <button class="modal-x" onclick="document.getElementById('tpl-modal').classList.remove('open')">×</button>
    </div>
    <div class="modal-body" id="tpl-modal-body">
      <div style="text-align:center;padding:20px;font-size:13px;color:var(--text-s);">Загрузка…</div>
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
  const CSRF = document.querySelector('meta[name="csrf-token"]').content;

  const PERIOD_LABEL = { 1: 'Каждый месяц', 2: 'Каждые 2 месяца', 3: 'Каждые 3 месяца', 6: 'Каждые 6 месяцев' };

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

  async function openTplModal(id) {
    const modal   = document.getElementById('tpl-modal');
    const titleEl = document.getElementById('tpl-modal-title');
    const subEl   = document.getElementById('tpl-modal-sub');
    const bodyEl  = document.getElementById('tpl-modal-body');
    modal.classList.add('open');
    titleEl.textContent = 'Загрузка…';
    subEl.textContent   = '';
    bodyEl.innerHTML    = '<div style="text-align:center;padding:20px;font-size:13px;color:var(--text-s);">Загрузка…</div>';

    try {
      const res = await fetch('/cabinet/templates/' + id, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      });
      if (!res.ok) throw new Error();
      const d = await res.json();

      titleEl.textContent = 'Шаблон счёта';
      subEl.textContent   = d.contractor?.name ?? '';

      const periodLabel = PERIOD_LABEL[d.period_months] ?? ('Каждые ' + d.period_months + ' мес.');
      const ndsLabel    = d.nds_rate > 0 ? d.nds_rate + '%' : 'Без НДС';

      const subtotal = d.items.reduce((s, it) => s + it.qty * it.price, 0);
      const ndsAmt   = Math.round(subtotal * d.nds_rate) / 100;
      const total    = subtotal + ndsAmt;

      const itemRows = d.items.map(it => `
        <tr>
          <td>${escHtml(it.name)}</td>
          <td class="tc">${escHtml(it.unit)}</td>
          <td class="tc">${it.qty % 1 === 0 ? it.qty : Number(it.qty).toFixed(2)}</td>
          <td class="tr">${fmtMoney(it.price)}</td>
          <td class="tr">${fmtMoney(it.qty * it.price)}</td>
        </tr>`).join('');

      bodyEl.innerHTML = `
        <div>
          <div class="md-section-title">Параметры расписания</div>
          <div class="md-options-grid">
            <div class="md-option">
              <div class="md-opt-label">Периодичность</div>
              <div class="md-opt-value">${escHtml(periodLabel)}</div>
            </div>
            <div class="md-option">
              <div class="md-opt-label">День месяца</div>
              <div class="md-opt-value">${d.day_of_month} число</div>
            </div>
            <div class="md-option">
              <div class="md-opt-label">Следующий запуск</div>
              <div class="md-opt-value">${escHtml(d.next_run_at ?? '—')}</div>
            </div>
            <div class="md-option">
              <div class="md-opt-label">Последний запуск</div>
              <div class="md-opt-value ${d.last_run_at ? '' : 'muted'}">${escHtml(d.last_run_at ?? 'Ещё не запускался')}</div>
            </div>
            <div class="md-option">
              <div class="md-opt-label">Добавлять акт</div>
              <div class="md-opt-value ${d.with_act ? 'green' : 'muted'}">${d.with_act ? 'Да' : 'Нет'}</div>
            </div>
            <div class="md-option">
              <div class="md-opt-label">НДС</div>
              <div class="md-opt-value">${escHtml(ndsLabel)}</div>
            </div>
          </div>
        </div>
        ${d.basis ? `
        <div>
          <div class="md-section-title">Основание</div>
          <div style="font-size:13.5px;color:var(--text-h);padding:10px 14px;background:var(--bg);border:1px solid var(--border);border-radius:var(--rad-sm);">${escHtml(d.basis)}</div>
        </div>` : ''}
        <div>
          <div class="md-section-title">Позиции счёта</div>
          <div style="border:1px solid var(--border);border-radius:var(--rad-sm);overflow:hidden;">
            <table class="md-items-table">
              <thead>
                <tr>
                  <th>Наименование</th>
                  <th style="text-align:center;">Ед.</th>
                  <th style="text-align:center;">Кол.</th>
                  <th style="text-align:right;">Цена</th>
                  <th style="text-align:right;">Сумма</th>
                </tr>
              </thead>
              <tbody>${itemRows}</tbody>
            </table>
            <div style="padding:10px 14px;background:var(--bg);border-top:1px solid var(--border);display:flex;flex-direction:column;gap:3px;">
              ${d.nds_rate > 0 ? `<div style="display:flex;justify-content:space-between;font-size:13px;color:var(--text-s);"><span>НДС ${d.nds_rate}%:</span><span style="font-weight:600;color:var(--text-h);">${fmtMoney(ndsAmt)}</span></div>` : ''}
              <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:700;color:var(--text-h);border-top:${d.nds_rate > 0 ? '1px solid var(--border)' : 'none'};padding-top:${d.nds_rate > 0 ? '6px' : '0'};margin-top:${d.nds_rate > 0 ? '4px' : '0'};"><span>Итого:</span><span>${fmtMoney(total)}</span></div>
            </div>
          </div>
        </div>`;
    } catch {
      bodyEl.innerHTML = '<div style="padding:20px;text-align:center;font-size:13px;color:var(--text-s);">Не удалось загрузить данные шаблона</div>';
    }
  }

  async function toggleTemplate(id, btn) {
    try {
      const res = await fetch('/cabinet/templates/' + id + '/toggle', {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      });
      if (!res.ok) throw new Error();
      const { is_active } = await res.json();
      const card = btn.closest('.tpl-card');
      const badge = card.querySelector('.tpl-badge');
      if (is_active) {
        card.classList.remove('inactive');
        badge.className = 'tpl-badge tpl-badge-active';
        badge.textContent = 'Активен';
        btn.className = 'tpl-act-btn pause';
        btn.textContent = 'Пауза';
      } else {
        card.classList.add('inactive');
        badge.className = 'tpl-badge tpl-badge-inactive';
        badge.textContent = 'Пауза';
        btn.className = 'tpl-act-btn resume';
        btn.textContent = 'Возобновить';
      }
      showToast(is_active ? 'Шаблон возобновлён' : 'Шаблон приостановлен');
    } catch {
      showToast('Ошибка соединения');
    }
  }

  async function deleteTemplate(id, btn) {
    if (!confirm('Удалить шаблон? Новые счета по нему больше не будут создаваться.')) return;
    try {
      const res = await fetch('/cabinet/templates/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      });
      if (res.ok) {
        showToast('Шаблон удалён');
        btn.closest('.tpl-card').remove();
        if (!document.querySelector('.tpl-card')) location.reload();
      } else {
        showToast('Не удалось удалить шаблон');
      }
    } catch {
      showToast('Ошибка соединения');
    }
  }

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.getElementById('tpl-modal').classList.remove('open');
  });
  document.getElementById('tpl-modal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
  });
</script>
@endpush
