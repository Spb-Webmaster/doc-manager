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
                <button class="tpl-edit-btn" onclick="openTplModal({{ $tpl->id }}, true)" title="Редактировать">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
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
      <button class="modal-x" onclick="closeTplModal()">×</button>
    </div>
    <div class="modal-body" id="tpl-modal-body">
      <div style="text-align:center;padding:20px;font-size:13px;color:var(--text-s);">Загрузка…</div>
    </div>
    <div class="modal-foot" id="tpl-modal-foot"></div>
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

  let tplData = null;   // данные открытого шаблона (ответ showTemplate)

  const NDS_LABEL = r => r > 0 ? r + '%' : 'Без НДС';

  function closeTplModal() {
    document.getElementById('tpl-modal').classList.remove('open');
  }

  async function openTplModal(id, edit = false) {
    const modal   = document.getElementById('tpl-modal');
    const titleEl = document.getElementById('tpl-modal-title');
    const subEl   = document.getElementById('tpl-modal-sub');
    const bodyEl  = document.getElementById('tpl-modal-body');
    const footEl  = document.getElementById('tpl-modal-foot');
    modal.classList.add('open');
    titleEl.textContent = 'Загрузка…';
    subEl.textContent   = '';
    footEl.innerHTML    = '';
    bodyEl.innerHTML    = '<div style="text-align:center;padding:20px;font-size:13px;color:var(--text-s);">Загрузка…</div>';

    try {
      const res = await fetch('/cabinet/templates/' + id, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      });
      if (!res.ok) throw new Error();
      tplData = await res.json();
      subEl.textContent = tplData.contractor?.name ?? '';
      edit ? renderTplEdit() : renderTplView();
    } catch {
      bodyEl.innerHTML = '<div style="padding:20px;text-align:center;font-size:13px;color:var(--text-s);">Не удалось загрузить данные шаблона</div>';
    }
  }

  /* ---------- Режим просмотра ---------- */
  function renderTplView() {
    const d       = tplData;
    const titleEl = document.getElementById('tpl-modal-title');
    const bodyEl  = document.getElementById('tpl-modal-body');
    const footEl  = document.getElementById('tpl-modal-foot');
    titleEl.textContent = 'Шаблон счёта';

    const periodLabel = PERIOD_LABEL[d.period_months] ?? ('Каждые ' + d.period_months + ' мес.');

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
            <div class="md-opt-value">${escHtml(NDS_LABEL(d.nds_rate))}</div>
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

    footEl.innerHTML = `
      <button type="button" class="btn btn-outline" onclick="closeTplModal()">Закрыть</button>
      <button type="button" class="btn btn-primary" onclick="renderTplEdit()">Редактировать</button>`;
  }

  /* ---------- Режим редактирования ---------- */
  function renderTplEdit() {
    const d       = tplData;
    const titleEl = document.getElementById('tpl-modal-title');
    const bodyEl  = document.getElementById('tpl-modal-body');
    const footEl  = document.getElementById('tpl-modal-foot');
    titleEl.textContent = 'Редактирование шаблона';

    const periodOpts = [1, 2, 3, 6].map(m =>
      `<option value="${m}" ${m == d.period_months ? 'selected' : ''}>${PERIOD_LABEL[m]}</option>`).join('');
    const ndsOpts = [0, 10, 20].map(r =>
      `<option value="${r}" ${r == d.nds_rate ? 'selected' : ''}>${NDS_LABEL(r)}</option>`).join('');

    bodyEl.innerHTML = `
      <form id="tpl-edit-form" onsubmit="saveTemplate(event)">
        <div class="md-form-error" id="tpl-edit-error"></div>

        <div class="md-section-title">Расписание</div>
        <div class="md-form-grid">
          <div class="field">
            <label class="field-label" for="te-next">Следующий запуск</label>
            <input class="field-input" type="date" id="te-next" name="next_run_at" value="${escHtml(d.next_run_iso ?? '')}" required>
            <div class="md-form-hint">Дата ближайшего счёта. Она же станет датой счёта и началом периода.</div>
          </div>
          <div class="field">
            <label class="field-label" for="te-day">День месяца</label>
            <input class="field-input" type="number" id="te-day" name="day_of_month" min="1" max="31" value="${d.day_of_month}" required>
            <div class="md-form-hint">День для всех последующих запусков.</div>
          </div>
          <div class="field">
            <label class="field-label" for="te-period">Периодичность</label>
            <select class="field-select" id="te-period" name="period_months">${periodOpts}</select>
          </div>
          <div class="field">
            <label class="field-label">Акт</label>
            <label class="md-check"><input type="checkbox" id="te-act" name="with_act" ${d.with_act ? 'checked' : ''}> Создавать акт вместе со счётом</label>
          </div>
        </div>

        <div class="md-section-title" style="margin-top:18px;">Счёт</div>
        <div class="md-form-grid">
          <div class="field" style="grid-column:1 / -1;">
            <label class="field-label" for="te-basis">Основание</label>
            <input class="field-input" type="text" id="te-basis" name="basis" maxlength="500" value="${escHtml(d.basis ?? '')}" placeholder="Договор № 26-1-1 от 01.01.2026">
          </div>
          <div class="field">
            <label class="field-label" for="te-nds">НДС</label>
            <select class="field-select" id="te-nds" name="nds_rate" onchange="recalcTplTotals()">${ndsOpts}</select>
          </div>
        </div>

        <div class="md-section-title" style="margin-top:18px;">Позиции</div>
        <div style="border:1px solid var(--border);border-radius:var(--rad-sm);overflow:hidden;margin-bottom:8px;">
          <table class="md-edit-table">
            <thead>
              <tr>
                <th>Наименование</th>
                <th style="width:64px;">Ед.</th>
                <th style="width:72px;">Кол.</th>
                <th style="width:110px;">Цена</th>
                <th style="width:100px;text-align:right;">Сумма</th>
                <th style="width:34px;"></th>
              </tr>
            </thead>
            <tbody id="te-items"></tbody>
          </table>
        </div>
        <button type="button" class="md-add-row" onclick="addTplItemRow()">+ Добавить позицию</button>

        <div class="md-totals" style="margin-top:14px;">
          <div class="md-totals-row" id="te-sub-row"><span>Сумма без НДС:</span><span id="te-subtotal"></span></div>
          <div class="md-totals-row" id="te-nds-row"><span>НДС <span id="te-nds-rate"></span>:</span><span id="te-nds-amt"></span></div>
          <div class="md-totals-row total"><span>Итого:</span><span id="te-total"></span></div>
        </div>
      </form>`;

    d.items.forEach(it => addTplItemRow(it));
    recalcTplTotals();

    footEl.innerHTML = `
      <button type="button" class="btn btn-outline" onclick="renderTplView()">Отмена</button>
      <button type="submit" form="tpl-edit-form" class="btn btn-primary" id="te-save">Сохранить</button>`;
  }

  function addTplItemRow(it = { name: '', unit: 'шт', qty: 1, price: 0 }) {
    const tbody = document.getElementById('te-items');
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><input class="field-input" type="text" data-f="name" maxlength="500" value="${escHtml(it.name)}" placeholder="Наименование" required></td>
      <td><input class="field-input" type="text" data-f="unit" maxlength="50" value="${escHtml(it.unit)}" required></td>
      <td><input class="field-input" type="number" data-f="qty" min="0" step="0.001" value="${it.qty}" required></td>
      <td><input class="field-input" type="number" data-f="price" min="0" step="0.01" value="${it.price}" required></td>
      <td class="tr" data-amount>${fmtMoney(it.qty * it.price)}</td>
      <td><button type="button" class="md-row-del" title="Удалить позицию" onclick="removeTplItemRow(this)">×</button></td>`;
    tr.querySelectorAll('input').forEach(i => i.addEventListener('input', recalcTplTotals));
    tbody.appendChild(tr);
    recalcTplTotals();
  }

  function removeTplItemRow(btn) {
    const tbody = document.getElementById('te-items');
    if (tbody.children.length <= 1) return;
    btn.closest('tr').remove();
    recalcTplTotals();
  }

  function collectTplItems() {
    return [...document.querySelectorAll('#te-items tr')].map(tr => ({
      name:  tr.querySelector('[data-f="name"]').value.trim(),
      unit:  tr.querySelector('[data-f="unit"]').value.trim(),
      qty:   parseFloat(tr.querySelector('[data-f="qty"]').value) || 0,
      price: parseFloat(tr.querySelector('[data-f="price"]').value) || 0,
    }));
  }

  function recalcTplTotals() {
    const tbody = document.getElementById('te-items');
    if (!tbody) return;
    let subtotal = 0;
    [...tbody.children].forEach(tr => {
      const qty   = parseFloat(tr.querySelector('[data-f="qty"]').value) || 0;
      const price = parseFloat(tr.querySelector('[data-f="price"]').value) || 0;
      const amt   = qty * price;
      subtotal += amt;
      tr.querySelector('[data-amount]').textContent = fmtMoney(amt);
    });
    const rate   = parseInt(document.getElementById('te-nds').value) || 0;
    const ndsAmt = Math.round(subtotal * rate) / 100;
    document.getElementById('te-subtotal').textContent = fmtMoney(subtotal);
    document.getElementById('te-nds-rate').textContent = rate + '%';
    document.getElementById('te-nds-amt').textContent  = fmtMoney(ndsAmt);
    document.getElementById('te-total').textContent    = fmtMoney(subtotal + ndsAmt);
    document.getElementById('te-sub-row').style.display = rate > 0 ? '' : 'none';
    document.getElementById('te-nds-row').style.display = rate > 0 ? '' : 'none';
    tbody.querySelectorAll('.md-row-del').forEach(b => b.disabled = tbody.children.length <= 1);
  }

  async function saveTemplate(e) {
    e.preventDefault();
    const errEl = document.getElementById('tpl-edit-error');
    const btn   = document.getElementById('te-save');
    errEl.textContent = '';

    const payload = {
      next_run_at:   document.getElementById('te-next').value,
      day_of_month:  parseInt(document.getElementById('te-day').value),
      period_months: parseInt(document.getElementById('te-period').value),
      with_act:      document.getElementById('te-act').checked,
      basis:         document.getElementById('te-basis').value.trim() || null,
      nds_rate:      parseInt(document.getElementById('te-nds').value),
      items:         collectTplItems(),
    };

    btn.disabled = true;
    btn.textContent = 'Сохранение…';
    try {
      const res = await fetch('/cabinet/templates/' + tplData.id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(payload),
      });
      if (res.status === 422) {
        const j = await res.json();
        errEl.textContent = Object.values(j.errors ?? {}).flat().join(' ') || j.message || 'Проверьте заполнение полей';
        return;
      }
      if (!res.ok) throw new Error();
      showToast('Шаблон сохранён');
      closeTplModal();
      setTimeout(() => location.reload(), 500);
    } catch {
      errEl.textContent = 'Не удалось сохранить шаблон. Попробуйте ещё раз.';
    } finally {
      btn.disabled = false;
      btn.textContent = 'Сохранить';
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
    if (e.key === 'Escape') closeTplModal();
  });
  document.getElementById('tpl-modal').addEventListener('click', function(e) {
    if (e.target === this) closeTplModal();
  });
</script>
@endpush
