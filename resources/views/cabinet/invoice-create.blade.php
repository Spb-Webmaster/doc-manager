@extends('layouts.cabinet')

@section('title', 'Новый счёт — СчётОк')

@section('content')

<header class="topbar topbar--form">
  <div class="tb-left">
    <div class="tb-breadcrumb">
      <a href="{{ route('cabinet') }}">Кабинет</a>
      <span class="tb-breadcrumb-sep">›</span>
      <span class="tb-current">Новый счёт</span>
    </div>
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
        <div class="req-empty-desc">Добавьте данные организации — они автоматически подставятся в счёт.</div>
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
        <div class="req-empty-desc">Добавьте банковский счёт — он будет указан в счёте для оплаты.</div>
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

    <!-- 3. Получатель счёта -->
    <div class="form-section">
      <div class="fs-head">
        <div class="fs-title"><div class="fs-num" id="step3-num">3</div>Получатель счёта</div>
      </div>
      <div class="tab-group" id="cp-tab-group">
        <button class="tab-btn active" data-target="pane-cp-list">Выбрать из списка</button>
        <button class="tab-btn" data-target="pane-cp-inn">Ввести ИНН</button>
      </div>
      <!-- Ввод ИНН -->
      <div id="pane-cp-inn" style="display:none">
        <div class="field" style="margin-bottom:10px;">
          <div class="field-label">ИНН контрагента</div>
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

    <!-- 3. Номер и дата -->
    <div class="form-section">
      <div class="fs-head">
        <div class="fs-title"><div class="fs-num" id="step4-num">4</div>Номер, дата и основание</div>
      </div>
      <div class="field-row-2" style="margin-bottom:14px;">
        <div class="field">
          <div class="field-label">Номер счёта</div>
          <div class="auto-num-row" id="num-auto-row">
            <span class="auto-num-val" id="num-display">1</span>
            <span class="auto-num-badge">авто</span>
            <button class="auto-num-link" id="num-manual-btn">Ввести вручную</button>
          </div>
          <input class="field-input" id="inv-num" type="text" value="1" style="display:none;">
        </div>
        <div class="field">
          <div class="field-label">Дата</div>
          <div class="date-wrap">
            <svg class="date-ico" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
              <rect x="1.5" y="2.5" width="13" height="12" rx="2"/><path d="M5 1.5v2M11 1.5v2M1.5 6.5h13"/>
            </svg>
            <input class="date-input" id="inv-date" type="text" readonly>
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
          <input class="field-input" id="inv-basis" type="text" placeholder="Договор № 26-1-1 от 01.01.2026">
        </div>
        <div id="pane-basis-list">
          <x-form.mz-select id="basis-select" placeholder="Без договора" />
          <button type="button" id="btn-create-contract" style="margin-top:8px;border:none;background:none;font-family:inherit;font-size:12.5px;color:var(--accent);cursor:pointer;padding:2px 0;display:flex;align-items:center;gap:5px;font-weight:500;">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M6 1v10M1 6h10"/></svg>
            Создать договор
          </button>
        </div>
      </div>
    </div>
    <div class="form-divider"></div>

    <!-- 4. Позиции -->
    <div class="form-section">
      <div class="fs-head">
        <div class="fs-title"><div class="fs-num" id="step5-num">5</div>Позиции счёта</div>
      </div>
      <div class="item-cards" id="items-body"></div>
      <button class="add-item-btn" id="add-item">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M6.5 1.5v10M1.5 6.5h10"/></svg>
        Добавить позицию
      </button>
    </div>
    <div class="form-divider"></div>

    <!-- 5. Итого -->
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

    <!-- 6. Подпись и печать -->
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
      <button class="tb-btn tb-btn-ghost" id="smart-invoice-btn" style="flex:1;height:48px;border-radius:var(--rad-sm);font-size:14px;justify-content:center;gap:7px;">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M8 1v2M8 13v2M1 8h2M13 8h2M3.22 3.22l1.42 1.42M11.36 11.36l1.42 1.42M3.22 12.78l1.42-1.42M11.36 4.64l1.42-1.42"/>
          <circle cx="8" cy="8" r="3"/>
        </svg>
        Умный счёт
      </button>
      <button class="tb-btn tb-btn-primary" id="save-invoice-btn" style="flex:2;height:48px;border-left:none;border-radius:var(--rad-sm);font-size:15px;justify-content:center;">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M13.5 10.5v2a1 1 0 0 1-1 1h-9a1 1 0 0 1-1-1v-2M8 2v8M5 7l3 3 3-3"/>
        </svg>
        Сохранить счёт
      </button>
    </div>

  </div><!-- /form-col -->

  <!-- ── PREVIEW ── -->
  <div class="preview-col">
    <div class="preview-label">Предпросмотр счёта</div>
    <div class="invoice-doc" id="inv-preview">

      <!-- Банковский блок -->
      <table class="bank-block">
        <tr>
          <td class="bb-main" rowspan="2">
            <div id="pv-bank-name">{{ $bank ? ($bank->bank . ($bank->city ? ' г. ' . $bank->city : '')) : '—' }}</div>
            <div class="bb-sublabel">Банк получателя</div>
          </td>
          <td class="bb-key">БИК</td>
          <td class="bb-val" id="pv-bank-bik">{{ $bank?->bik ?? '—' }}</td>
        </tr>
        <tr>
          <td class="bb-key">Сч. №</td>
          <td class="bb-val" id="pv-bank-corr">{{ $bank?->correspondent_account ?? '—' }}</td>
        </tr>
        <tr>
          <td class="bb-recip">
            <div>ИНН {{ $reqData['inn'] ?? '—' }}{{ ($reqData && !isset($reqData['kpp'])) ? '' : '' }}</div>
            <div>{{ $reqData['name'] ?? '—' }}</div>
            <div class="bb-sublabel">Получатель</div>
          </td>
          <td class="bb-key">Сч. №</td>
          <td class="bb-val" id="pv-bank-payment">{{ $bank?->payment_account ?? '—' }}</td>
        </tr>
      </table>

      <!-- Заголовок -->
      <div class="inv-title">Счёт на оплату № <span id="pv-num">1</span> от <span id="pv-date">—</span></div>
      <div class="inv-divider"></div>

      <!-- Стороны -->
      <div class="inv-party-row">
        <div class="inv-pk">Поставщик<br>(Исполнитель):</div>
        <div class="inv-pv">{{ $reqData ? ($reqData['name'] . ', ИНН ' . $reqData['inn']) : '—' }}</div>
      </div>
      <div class="inv-party-row">
        <div class="inv-pk">Покупатель<br>(Заказчик):</div>
        <div class="inv-pv" id="pv-cp-name"><span class="inv-placeholder">Введите ИНН контрагента</span></div>
      </div>
      <div class="inv-basis-row">
        <span class="inv-bk">Основание:</span><span class="inv-bv" id="pv-basis">&nbsp;</span>
      </div>

      <!-- Таблица позиций -->
      <table class="doc-items-table">
        <thead>
          <tr>
            <th style="width:26px;">№</th>
            <th>Товары (работы, услуги)</th>
            <th style="width:44px;">Кол-во</th>
            <th style="width:30px;">Ед.</th>
            <th style="width:72px;">Цена</th>
            <th style="width:72px;">Сумма</th>
          </tr>
        </thead>
        <tbody id="pv-items">
          <tr><td colspan="6" class="empty-row">Добавьте позиции счёта</td></tr>
        </tbody>
      </table>

      <!-- Итого -->
      <div class="inv-totals-wrap">
        <table class="inv-totals">
          <tr class="inv-total-row"><td>Итого:</td><td id="pv-sub">0,00</td></tr>
          <tr class="inv-total-row" id="pv-vat-row" style="display:none"><td id="pv-vat-label">НДС 20%:</td><td id="pv-vat">0,00</td></tr>
          <tr class="inv-total-row" id="pv-novat-row"><td>Без налога (НДС)</td><td>-</td></tr>
          <tr class="inv-total-row inv-grand-total"><td>Всего к оплате:</td><td id="pv-total">0,00</td></tr>
        </table>
      </div>

      <!-- Сводка -->
      <div class="inv-summary">
        <div class="inv-count" id="pv-count">Всего наименований 0, на сумму 0,00 руб.</div>
        <div class="inv-words" id="pv-words">Ноль рублей 00 копеек</div>
      </div>

      <!-- Подпись -->
      <div class="inv-sig-area">
        <div class="inv-sig-row">
          <span class="inv-sig-role">{{ $reqData ? ($reqData['ogrn_label'] === 'ОГРНИП' ? 'Предприниматель' : 'Руководитель') : 'Предприниматель' }}</span>
          <span class="inv-sig-line-el"></span>
        </div>
        <div class="inv-stamp">
          <div class="inv-stamp-circle"></div>
        </div>
      </div>

    </div>
  </div>

</div><!-- /create-layout -->

<!-- MODAL: Умный счёт -->
<div class="modal-overlay" id="smart-invoice-modal">
  <div class="inv-modal" style="max-width:460px;">
    <div class="modal-head">
      <div>
        <div class="modal-title">Умный счёт</div>
        <div class="modal-sub">Автоматическое создание по расписанию</div>
      </div>
      <button class="modal-x" data-close="smart-invoice-modal">×</button>
    </div>
    <div class="modal-body">

      <!-- Периодичность -->
      <div class="si-section">
        <div class="si-section-label">Периодичность</div>
        <div class="si-period-grid">
          <label class="si-period-card active" data-months="1">
            <input type="radio" name="si-period" value="1" checked>
            <div class="si-period-label">Каждый месяц</div>
            <div class="si-period-sub">12 раз в год</div>
          </label>
          <label class="si-period-card" data-months="2">
            <input type="radio" name="si-period" value="2">
            <div class="si-period-label">Каждые 2 месяца</div>
            <div class="si-period-sub">6 раз в год</div>
          </label>
          <label class="si-period-card" data-months="3">
            <input type="radio" name="si-period" value="3">
            <div class="si-period-label">Каждые 3 месяца</div>
            <div class="si-period-sub">4 раза в год</div>
          </label>
          <label class="si-period-card" data-months="6">
            <input type="radio" name="si-period" value="6">
            <div class="si-period-label">Каждые 6 месяцев</div>
            <div class="si-period-sub">2 раза в год</div>
          </label>
        </div>
      </div>

      <!-- День месяца -->
      <div class="si-section">
        <div class="si-section-label">Число месяца</div>
        <div class="si-day-wrap">
          <input class="si-day-input" id="si-day" type="number" min="1" max="31" value="1">
          <span class="si-day-hint">Счёт будет формироваться в это число каждого периода</span>
        </div>
      </div>

      <!-- Добавить акт -->
      <div class="si-section">
        <div class="si-section-label">Добавить акт</div>
        <div class="si-toggle">
          <button class="si-toggle-btn active" id="si-act-yes">Да</button>
          <button class="si-toggle-btn" id="si-act-no">Нет</button>
        </div>
      </div>

      <!-- Пометка -->
      <div class="si-note">
        <strong>Как это работает:</strong> данный счёт будет автоматически формироваться в указанный период и сохраняться в ваших счетах.
        <span style="color:var(--red);">НЕ НУЖНО указывать даты в позициях счета, они формируются автоматически.</span>
      </div>

    </div>
    <div class="modal-foot" style="justify-content:flex-end;">
      <button class="btn" id="si-cancel-btn" style="font-size:14px;">Отмена</button>
      <button class="btn btn-primary" id="si-save-btn" style="font-size:14px;">Сохранить</button>
    </div>
  </div>
</div>

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

<x-cabinet.contract-modal />

@endsection

@push('scripts')
<script>
(function () {
  /* ── Server data ── */
  const CONTRACTORS = @json($contractors);

  const DADATA_URL = '{{ route('dadata.party') }}';
  const CSRF       = document.querySelector('meta[name="csrf-token"]').content;

  /* ── State ── */
  let cpData = null;
  let innTimer = null;
  let items = [{ name: '', unit: 'шт.', qty: 1, price: 0 }];
  let selectedDate = new Date();
  let calViewing = new Date();
  let selectedContractorId  = null;
  let selectedContractId    = null;
  let stampBase64           = null;
  let stampScale            = 100;
  let signatureBase64       = null;
  let signatureScale        = 100;
  let selectedBankAccountId = {{ $bank?->id ?? 'null' }};
  const PRESELECTED_CONTRACTOR_ID = {{ $preselectedContractorId ?? 'null' }};

  /* ── INN lookup ── */
  const cpInn   = document.getElementById('cp-inn');
  const spinner = document.getElementById('inn-spinner');
  const okIcon  = document.getElementById('inn-ok');
  const errIcon = document.getElementById('inn-err');
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
        document.getElementById('inv-num').value = 1;
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
  const cpSelect = document.getElementById('cp-select');
  const cpResultList = document.getElementById('cp-result-list');

  cpSelect.addEventListener('change', () => {
    const id = +cpSelect.value;
    if (!id) { cpData = null; selectedContractorId = null; cpResultList.classList.remove('show'); cpResultList.innerHTML = ''; refreshBasisContracts([]); updatePreview(); return; }
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
  const dateInp = document.getElementById('inv-date');

  function formatDate(d) {
    return d.toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' });
  }
  function formatDateShort(d) {
    return d.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' });
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
      const sel = selectedDate && date.toDateString() === selectedDate.toDateString();
      const tod = date.toDateString() === new Date().toDateString();
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
  function renderItems() {
    const body = document.getElementById('items-body');
    body.innerHTML = items.map((it, i) => `
      <div class="item-card">
        <div class="item-name-row">
          <span class="item-name-num">${i + 1}</span>
          <input class="item-name-input" data-i="${i}" data-f="name" type="text"
            placeholder="Наименование товара или услуги" value="${escHtml(it.name)}">
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
        const idx = +inp.dataset.i;
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

  function escHtml(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;'); }

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
    document.getElementById('pv-total').textContent = fmtMoneyPlain(total);
    document.getElementById('pv-vat-row').style.display   = vat > 0 ? '' : 'none';
    document.getElementById('pv-novat-row').style.display = vat > 0 ? 'none' : '';
    document.getElementById('pv-vat-label').textContent   = 'НДС ' + vat + '%:';

    const cnt = items.filter(it => it.name || it.price).length;
    document.getElementById('pv-count').textContent =
      'Всего наименований ' + cnt + ', на сумму ' + fmtMoneyPlain(total) + ' руб.';
    document.getElementById('pv-words').textContent = numToWords(Math.round(total));
  }

  document.getElementById('vat-select').addEventListener('change', updateTotals);

  /* ── Preview ── */
  function updatePreview() {
    document.getElementById('pv-num').textContent  = document.getElementById('inv-num').value;
    document.getElementById('pv-date').textContent = selectedDate ? formatDate(selectedDate) : '—';

    const basis = document.getElementById('inv-basis').value.trim();
    document.getElementById('pv-basis').textContent = basis || ' ';

    if (cpData) {
      let cpStr = cpData.name + ', ИНН ' + (cpInn.value || (cpSelect ? cpSelect.options[cpSelect.selectedIndex]?.text?.split('·')[1]?.trim() : ''));
      if (cpData.kpp) cpStr += ', КПП ' + cpData.kpp;
      if (cpData.addr) cpStr += ', ' + cpData.addr;
      document.getElementById('pv-cp-name').textContent = cpStr;
    } else {
      document.getElementById('pv-cp-name').innerHTML = '<span class="inv-placeholder">Введите ИНН контрагента</span>';
    }
  }

  function renderPreviewItems() {
    if (!items.length) {
      document.getElementById('pv-items').innerHTML =
        '<tr><td colspan="6" class="empty-row">Добавьте позиции счёта</td></tr>';
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
    const ones = ['','один','два','три','четыре','пять','шесть','семь','восемь','девять'];
    const teens = ['десять','одиннадцать','двенадцать','тринадцать','четырнадцать','пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать'];
    const tens  = ['','','двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят','восемьдесят','девяносто'];
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
    if (mil) { result += threeDigits(mil) + ' ' + plural(mil, 'миллион', 'миллиона', 'миллионов') + ' '; }
    if (th)  { result += threeDigits(th, true) + ' ' + plural(th, 'тысяча', 'тысячи', 'тысяч') + ' '; }
    if (hun) { result += threeDigits(hun) + ' '; }
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

    const cityStr = opt.dataset.city ? ' г. ' + opt.dataset.city : '';
    document.getElementById('pv-bank-name').textContent    = (opt.dataset.bank || '—') + cityStr;
    document.getElementById('pv-bank-bik').textContent     = opt.dataset.bik     || '—';
    document.getElementById('pv-bank-corr').textContent    = opt.dataset.corr    || '—';
    document.getElementById('pv-bank-payment').textContent = opt.dataset.payment  || '—';
  }

  /* ── Basis contracts ── */
  function refreshBasisContracts(list, autoSelectFirst = false, selectContractId = null) {
    const sel  = document.getElementById('basis-select');
    const root = sel?.closest('[data-mz-select]');
    if (!root) return;
    const dropdown = root.querySelector('.mz-select__dropdown');
    const trigger  = root.querySelector('.mz-select__trigger');

    while (sel.options.length > 1) sel.remove(1);
    list.forEach(ct => {
      const label = ct.name + ' № ' + ct.number;
      const [y, m, d] = (ct.date || '').split('-');
      const dateStr = d ? `${d}.${m}.${y}` : '';
      const value = dateStr ? `${ct.name} № ${ct.number} от ${dateStr}` : `${ct.name} № ${ct.number}`;
      const opt = new Option(label, value);
      opt.dataset.contractId = ct.id;
      sel.add(opt);
    });

    let selectIdx = 0;
    if (selectContractId) {
      for (let i = 1; i < sel.options.length; i++) {
        if (+sel.options[i].dataset.contractId === selectContractId) { selectIdx = i; break; }
      }
    }
    if (!selectIdx && autoSelectFirst && sel.options.length > 1) selectIdx = 1;
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
    document.getElementById('inv-basis').value = selectIdx > 0 ? chosenOpt.value : 'Без договора';
    selectedContractId = selectIdx > 0 ? (+chosenOpt.dataset.contractId || null) : null;
    updatePreview();
  }

  async function fetchBasisContracts(contractorId, autoSelectFirst = false, selectContractId = null) {
    if (!contractorId) { refreshBasisContracts([]); return; }
    try {
      const res = await fetch('/cabinet/contractors/' + contractorId + '/contracts', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      });
      refreshBasisContracts(res.ok ? await res.json() : [], autoSelectFirst, selectContractId);
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

  document.getElementById('inv-num').addEventListener('input', updatePreview);
  document.getElementById('inv-basis').addEventListener('input', updatePreview);

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
    document.getElementById('inv-basis').value = this.value || 'Без договора';
    const opt = this.options[this.selectedIndex];
    selectedContractId = opt?.dataset.contractId ? +opt.dataset.contractId : null;
    updatePreview();
  });

  /* ── Auto number ── */
  async function fetchNextNumber() {
    if (document.getElementById('num-auto-row').style.display === 'none') return;
    try {
      const url = new URL('{{ route('cabinet.invoices.next-number') }}', location.origin);
      if (selectedContractorId) url.searchParams.set('contractor_id', selectedContractorId);
      const res = await fetch(url, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      });
      if (!res.ok) return;
      const { number } = await res.json();
      document.getElementById('num-display').textContent = number;
      document.getElementById('inv-num').value = number;
      updatePreview();
    } catch {}
  }

  document.getElementById('num-manual-btn').addEventListener('click', () => {
    document.getElementById('num-auto-row').style.display = 'none';
    const inp = document.getElementById('inv-num');
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

  /* ── File upload ── */
  setupUpload('sig-zone', 'sig-file', 'sig-preview-wrap', 'sig-preview-img', 'sig-re', 'sig-apply', 'open-sig-modal', (src, scale) => {
    signatureBase64 = src;
    signatureScale  = scale;
    const lineEl = document.querySelector('.inv-sig-line-el');
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
    stampBase64 = src;
    stampScale  = scale;
    const circle = document.querySelector('.inv-stamp-circle');
    if (circle) {
      const size = Math.round(80 * scale / 100) + 'px';
      circle.style.width  = size;
      circle.style.height = size;
      circle.innerHTML = '<img class="inv-stamp-img" src="' + src + '" alt="Печать" style="max-width:' + size + ';max-height:' + size + ';">';
    }
    document.getElementById('stamp-sub').textContent = 'Печать добавлена';
  });

  document.getElementById('save-invoice-btn').addEventListener('click', async function () {
    if (!selectedContractorId && !cpData) {
      alert('Выберите получателя счёта');
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
      number:          document.getElementById('inv-num').value.trim(),
      date:            dateStr,
      basis:           document.getElementById('inv-basis').value.trim() || null,
      nds_rate:        +document.getElementById('vat-select').value,
      items:           validItems.map(it => ({
        name:  it.name,
        unit:  it.unit,
        qty:   it.qty,
        price: it.price,
      })),
      stamp_image:     stampBase64,
      stamp_scale:     stampScale,
      signature_image: signatureBase64,
      signature_scale: signatureScale,
    };

    try {
      const res  = await fetch('{{ route('cabinet.invoices.store') }}', {
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

  /* ── Smart Invoice modal ── */
  const siModal    = document.getElementById('smart-invoice-modal');
  const siSaveBtn  = document.getElementById('si-save-btn');
  const siCancelBtn = document.getElementById('si-cancel-btn');
  const siActYes   = document.getElementById('si-act-yes');
  const siActNo    = document.getElementById('si-act-no');
  let siWithAct    = true;

  document.getElementById('smart-invoice-btn').addEventListener('click', () => {
    if (!selectedContractorId && !cpData) { alert('Выберите получателя счёта'); return; }
    const validItems = items.filter(it => it.name.trim() || it.price > 0);
    if (!validItems.length) { alert('Добавьте хотя бы одну позицию'); return; }
    siModal.classList.add('open');
  });

  siCancelBtn.addEventListener('click', () => siModal.classList.remove('open'));
  siModal.querySelector('.modal-x').addEventListener('click', () => siModal.classList.remove('open'));

  siActYes.addEventListener('click', () => {
    siWithAct = true;
    siActYes.classList.add('active');
    siActNo.classList.remove('active');
  });
  siActNo.addEventListener('click', () => {
    siWithAct = false;
    siActNo.classList.add('active');
    siActYes.classList.remove('active');
  });

  document.querySelectorAll('.si-period-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.si-period-card').forEach(c => c.classList.remove('active'));
      card.classList.add('active');
      card.querySelector('input').checked = true;
    });
  });

  const siDayInput = document.getElementById('si-day');
  siDayInput.addEventListener('input', () => {
    let v = parseInt(siDayInput.value) || 1;
    if (v < 1)  v = 1;
    if (v > 31) v = 31;
    siDayInput.value = v;
  });

  siSaveBtn.addEventListener('click', async function () {
    const periodMonths = +document.querySelector('.si-period-card.active input').value;
    const dayOfMonth   = parseInt(siDayInput.value) || 1;
    const validItems   = items.filter(it => it.name.trim() || it.price > 0);

    const origHtml = this.innerHTML;
    this.textContent = 'Сохраняем…';
    this.disabled = true;

    const d = selectedDate;
    const dateStr = d
      ? `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`
      : null;

    const invoicePayload = {
      ...(selectedContractorId
        ? { contractor_id: selectedContractorId }
        : { new_contractor: { inn: cpInn.value, name: cpData.name, kpp: cpData.kpp || null, ogrn: cpData.ogrn || null, address: cpData.addr || null } }),
      contract_id:     selectedContractId,
      bank_account_id: selectedBankAccountId,
      number:          document.getElementById('inv-num').value.trim(),
      date:            dateStr,
      basis:           document.getElementById('inv-basis').value.trim() || null,
      nds_rate:        +document.getElementById('vat-select').value,
      items:           validItems.map(it => ({ name: it.name, unit: it.unit, qty: it.qty, price: it.price })),
      stamp_image:     stampBase64,
      stamp_scale:     stampScale,
      signature_image: signatureBase64,
      signature_scale: signatureScale,
    };

    try {
      // 1. Сохраняем счёт
      const invRes  = await fetch('{{ route('cabinet.invoices.store') }}', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body:    JSON.stringify(invoicePayload),
      });
      const invJson = await invRes.json();
      if (!invRes.ok) {
        const first = invJson.errors ? Object.values(invJson.errors)[0] : null;
        alert(Array.isArray(first) ? first[0] : (invJson.message ?? 'Ошибка сохранения счёта'));
        this.innerHTML = origHtml;
        this.disabled  = false;
        return;
      }

      // 2. Сохраняем умный счёт (contractor уже существует — используем id из ответа)
      const smartPayload = {
        contractor_id:   invJson.contractor_id,
        bank_account_id: selectedBankAccountId,
        basis:           document.getElementById('inv-basis').value.trim() || null,
        nds_rate:        +document.getElementById('vat-select').value,
        items:           validItems.map(it => ({ name: it.name, unit: it.unit, qty: it.qty, price: it.price })),
        period_months:   periodMonths,
        day_of_month:    dayOfMonth,
        with_act:        siWithAct,
      };
      await fetch('{{ route('cabinet.smart-invoices.store') }}', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body:    JSON.stringify(smartPayload),
      });

      // 3. Редирект как у обычного сохранения
      window.location.href = invJson.redirect ?? '{{ route('cabinet') }}';
    } catch {
      alert('Ошибка соединения');
      this.innerHTML = origHtml;
      this.disabled  = false;
    }
  });

  /* ── Contract modal (компонент x-cabinet.contract-modal) ── */
  document.getElementById('btn-create-contract').addEventListener('click', () => {
    if (!selectedContractorId) { alert('Сначала выберите получателя счёта'); return; }
    openContractModal(selectedContractorId);
  });

  document.addEventListener('contract:created', async ({ detail }) => {
    const listTab = document.querySelector('#basis-tab-group .tab-btn[data-target="pane-basis-list"]');
    if (listTab && !listTab.classList.contains('active')) listTab.click();
    await fetchBasisContracts(detail.contractorId, false, detail.contract?.id);
  });

})();
</script>
@endpush
