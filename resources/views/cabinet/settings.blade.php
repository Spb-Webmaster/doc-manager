@extends('layouts.cabinet')

@section('title', 'Настройки — СчётОк')

@section('content')
<div class="main-area settings-page">

  <header class="topbar">
    <span class="tb-title">Настройки</span>
  </header>

  <div class="content">

    <!-- Левая навигация по вкладкам -->
    <nav class="tabs-nav">
      <div class="tabs-nav-title">Разделы</div>
      <button class="tab-link active" data-tab="tab-org">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
          <rect x="1.5" y="2" width="12" height="11" rx="2"/>
          <path d="M5 2v11M1.5 6h4"/>
        </svg>
        Реквизиты
      </button>
      <button class="tab-link" data-tab="tab-bank">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
          <rect x="1" y="4" width="13" height="9" rx="1.5"/>
          <path d="M1 7.5h13M4.5 10.5h2"/>
        </svg>
        Банковские счета
      </button>
      <button class="tab-link" data-tab="tab-profile">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="7.5" cy="5" r="3"/>
          <path d="M1.5 14c0-3 2.7-5.5 6-5.5s6 2.5 6 5.5"/>
        </svg>
        Профиль и безопасность
      </button>
      <div class="tabs-sep"></div>
      <button class="tab-link" data-tab="tab-notif">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
          <path d="M7.5 1.5C5 1.5 3 3.5 3 6c0 4-1.5 5.5-1.5 5.5h12S12 10 12 6c0-2.5-2-4.5-4.5-4.5z"/>
          <path d="M6 12c0 .8.7 1.5 1.5 1.5S9 12.8 9 12"/>
        </svg>
        Уведомления
      </button>
    </nav>

    <!-- Панели вкладок -->
    <div class="tab-panels">

      <!-- ① Реквизиты -->
      <div class="tab-panel active" id="tab-org">
        <div class="settings-card">
          <div class="sc-head">
            <div>
              <div class="sc-title">Данные организации / ИП</div>
              <div class="sc-sub">Заполняются автоматически по ИНН из реестра ФНС</div>
            </div>
          </div>
          <div class="sc-body">
            <div class="field">
              <div class="field-label">ИНН</div>
              <div class="field-inline">
                <input class="field-input" id="org-inn" type="text" value="{{ $requisites['inn'] ?? '' }}" maxlength="12">
                <button class="btn btn-outline" id="inn-refresh-btn">
                  <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 7A6 6 0 0 1 12.5 4M13 7A6 6 0 0 1 1.5 10"/>
                    <path d="M13 2.5V5H10.5M1 11.5V9H3.5"/>
                  </svg>
                  Обновить из ФНС
                </button>
              </div>
            </div>
            <div class="inn-chip" id="fns-chip" style="{{ !empty($requisites) ? 'display:flex' : 'display:none' }};">
              <svg class="inn-chip-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <circle cx="8" cy="8" r="7" fill="#E6F7EF"/>
                <path d="M5 8l2.5 2.5L11 5.5" stroke="#159B6A" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <div class="inn-chip-text" id="fns-chip-text">@if(!empty($requisites))Реквизиты сохранены: <strong>{{ $requisites['short'] ?: $requisites['name'] }}</strong>@endif</div>
            </div>
            <div class="field-grid-2">
              <div class="field full">
                <div class="field-label">Полное наименование</div>
                <input class="field-input" id="org-name" type="text" value="{{ $requisites['name'] ?? '' }}">
              </div>
              <div class="field">
                <div class="field-label">Краткое наименование</div>
                <input class="field-input" id="org-short" type="text" value="{{ $requisites['short'] ?? '' }}">
              </div>
              <div class="field">
                <div class="field-label">ОГРНИП / ОГРН</div>
                <input class="field-input" id="org-ogrn" type="text" value="{{ $requisites['ogrn'] ?? '' }}">
              </div>
              <div class="field">
                <div class="field-label">КПП</div>
                <input class="field-input" id="org-kpp" type="text" placeholder="Для ООО/АО" value="{{ $requisites['kpp'] ?? '' }}">
                <div class="field-hint">Только для юридических лиц</div>
              </div>
              <div class="field full">
                <div class="field-label">Юридический адрес</div>
                <input class="field-input" id="org-addr" type="text" value="{{ $requisites['address'] ?? '' }}">
              </div>
              <div class="field">
                <div class="field-label">Телефон</div>
                <input class="field-input imask" id="org-phone" type="tel" value="{{ format_phone($requisites['phone'] ?? '') }}">
              </div>
              <div class="field">
                <div class="field-label">Email для документов</div>
                <input class="field-input" id="org-email" type="email" value="{{ $requisites['email'] ?? '' }}">
              </div>
            </div>
          </div>
          <div class="sc-foot">
            <button class="btn btn-outline" id="org-cancel">Отменить</button>
            <button class="btn btn-primary" id="org-save">Сохранить реквизиты</button>
          </div>
        </div>
      </div>

      <!-- ② Банковские счета -->
      <div class="tab-panel" id="tab-bank">
        <div class="settings-card">
          <div class="sc-head">
            <div>
              <div class="sc-title">Банковские счета</div>
              <div class="sc-sub">Основной счёт подставляется в счёт автоматически</div>
            </div>
          </div>
          <div class="sc-body" id="bank-list">
            @foreach($bankAccounts as $ba)
            <div class="bank-item{{ $ba->is_primary ? ' primary' : '' }}" id="bank-card-{{ $ba->id }}">
              <div class="bank-item-head" data-bank="{{ $ba->id }}">
                <div class="bank-drag-handle" title="Перетащить">
                  <svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor"><circle cx="3" cy="2.5" r="1.2"/><circle cx="7" cy="2.5" r="1.2"/><circle cx="3" cy="7" r="1.2"/><circle cx="7" cy="7" r="1.2"/><circle cx="3" cy="11.5" r="1.2"/><circle cx="7" cy="11.5" r="1.2"/></svg>
                </div>
                <div class="bank-item-head-ico">
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="14" height="10" rx="1.5"/><path d="M1 8h14M5 11.5h2.5"/></svg>
                </div>
                <div class="bank-item-head-info">
                  <div class="bank-item-name">{{ $ba->bank }}</div>
                  <div class="bank-item-acc">{{ $ba->payment_account }}</div>
                </div>
                @if($ba->is_primary)
                  <span class="bank-primary-badge">Основной</span>
                @endif
                <svg class="bank-chevron" width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="transition:transform .2s;margin-left:auto;flex-shrink:0;"><path d="M3.5 5.5l3.5 3.5 3.5-3.5"/></svg>
              </div>
              <div class="bank-collapse" id="bank-body-{{ $ba->id }}">
                <div class="bank-item-body">
                  <div class="bank-fields">
                    <div class="field">
                      <div class="field-label">БИК</div>
                      <input class="field-input" type="text" readonly value="{{ $ba->bik }}">
                    </div>
                    <div class="field">
                      <div class="field-label">Расчётный счёт</div>
                      <input class="field-input" type="text" readonly value="{{ $ba->payment_account }}">
                    </div>
                    <div class="field">
                      <div class="field-label">Корреспондентский счёт</div>
                      <input class="field-input" type="text" readonly value="{{ $ba->correspondent_account }}">
                    </div>
                    <div class="field">
                      <div class="field-label">Город</div>
                      <input class="field-input" type="text" readonly value="{{ $ba->city }}">
                    </div>
                  </div>
                </div>
                <div class="bank-item-foot">
                  <span></span>
                  <button class="btn btn-danger btn-delete-bank" data-id="{{ $ba->id }}" style="font-size:12.5px;padding:7px 14px;">
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3.5h9M5 3.5v-1h3v1M10 3.5l-.7 7H3.7l-.7-7"/></svg>
                    Удалить
                  </button>
                </div>
              </div>
            </div>
            @endforeach
          </div>

          <!-- Форма добавления -->
          <div class="bank-add-form" id="bank-add-form" style="display:none;">
            <div class="bank-item-body">
              <div style="display:flex;flex-direction:column;gap:16px;">
                <div class="field">
                  <div class="field-label">БИК банка</div>
                  <div class="field-inline">
                    <input class="field-input" id="new-bank-bik" type="text" placeholder="044525974" maxlength="9">
                    <button class="btn btn-outline" id="bik-lookup-btn">
                      <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 7A6 6 0 0 1 12.5 4M13 7A6 6 0 0 1 1.5 10"/>
                        <path d="M13 2.5V5H10.5M1 11.5V9H3.5"/>
                      </svg>
                      Найти банк
                    </button>
                  </div>
                </div>
                <div class="bank-fields">
                  <div class="field full">
                    <div class="field-label">Наименование банка</div>
                    <input class="field-input" id="new-bank-name" type="text" placeholder="АО «ТБанк»">
                  </div>
                  <div class="field">
                    <div class="field-label">Расчётный счёт</div>
                    <input class="field-input" id="new-bank-payment" type="text" placeholder="40802810000000000000" maxlength="20">
                  </div>
                  <div class="field">
                    <div class="field-label">Корреспондентский счёт</div>
                    <input class="field-input" id="new-bank-corr" type="text" placeholder="30101810000000000000" maxlength="20">
                  </div>
                  <div class="field full">
                    <div class="field-label">Город</div>
                    <input class="field-input" id="new-bank-city" type="text" placeholder="г. Москва">
                  </div>
                </div>
              </div>
            </div>
            <div class="bank-item-foot">
              <button class="btn btn-outline" id="add-bank-cancel" style="font-size:12.5px;padding:7px 14px;">Отмена</button>
              <button class="btn btn-primary" id="add-bank-save" style="font-size:12.5px;padding:7px 14px;">Сохранить счёт</button>
            </div>
          </div>

          <div class="sc-body" style="padding-top:0;">
            <button class="add-bank-btn" id="add-bank-btn">
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M7 1.5v11M1.5 7h11"/></svg>
              Добавить банковский счёт
            </button>
          </div>
        </div>
      </div>

      <!-- ③ Профиль и безопасность -->
      <div class="tab-panel" id="tab-profile">
        <div class="settings-card">
          <div class="sc-head">
            <div class="sc-title">Личные данные</div>
          </div>
          <div class="sc-body">
            <div class="field-grid-2">
              <div class="field">
                <div class="field-label">Имя</div>
                <input class="field-input" id="profile-name" type="text" value="{{ auth()->user()->name }}" maxlength="255">
              </div>
              <div class="field">
                <div class="field-label">Телефон</div>
                <input class="field-input imask" id="profile-phone" type="tel" value="{{ auth()->user()->phone ? format_phone(auth()->user()->phone) : '' }}">
              </div>
              <div class="field full">
                <div class="field-label">Email (логин)</div>
                <input class="field-input" id="profile-email" type="email" value="{{ auth()->user()->email }}">
              </div>
            </div>
          </div>
          <div class="sc-foot">
            <button class="btn btn-primary" id="profile-save-btn">Сохранить</button>
          </div>
        </div>

        <div class="settings-card">
          <div class="sc-head">
            <div class="sc-title">Смена пароля</div>
          </div>
          <div class="sc-body">
            <div class="field">
              <div class="field-label">Новый пароль</div>
              <div class="pw-wrap">
                <input class="field-input" id="pw-new" type="password" placeholder="Не менее 5 символов" style="padding-right:40px;">
                <button class="pw-eye" data-target="pw-new">
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4">
                    <path d="M1.5 8s2.5-4.5 6.5-4.5 6.5 4.5 6.5 4.5-2.5 4.5-6.5 4.5S1.5 8 1.5 8z" stroke-linecap="round"/>
                    <circle cx="8" cy="8" r="2"/>
                  </svg>
                </button>
              </div>
              <div class="pw-bars">
                <div class="pw-bar" id="bar1"></div>
                <div class="pw-bar" id="bar2"></div>
                <div class="pw-bar" id="bar3"></div>
              </div>
              <div class="pw-label" id="pw-lbl"></div>
            </div>
            <div class="field">
              <div class="field-label">Повторите новый пароль</div>
              <input class="field-input" id="pw-rep" type="password" placeholder="Повторите новый пароль">
            </div>
          </div>
          <div class="sc-foot">
            <button class="btn btn-primary" id="pw-save-btn">Изменить пароль</button>
          </div>
        </div>

        <div class="settings-card">
          <div class="sc-head">
            <div class="sc-title">Опасная зона</div>
          </div>
          <div class="sc-body">
            <div class="danger-zone" id="dz-container">
              @if(auth()->user()->account === 'delete')
                <div class="dz-title" style="color:var(--text-s)">Аккаунт помечен на удаление</div>
                <div class="dz-desc">Ваш запрос на удаление аккаунта принят. Все данные будут удалены после обработки администратором.</div>
              @else
                <div class="dz-title">Удалить аккаунт</div>
                <div class="dz-desc">Все ваши данные, реквизиты и история документов будут безвозвратно удалены. Это действие нельзя отменить.</div>
                <button class="btn btn-danger" id="dz-delete-btn">Удалить аккаунт</button>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- ④ Уведомления -->
      <div class="tab-panel" id="tab-notif">
        <div class="settings-card">
          <div class="sc-head">
            <div class="sc-title">Email-уведомления</div>
          </div>
          <div class="sc-body">
            <div class="notif-row">
              <div class="notif-info">
                <div class="notif-title">Счёт создан по шаблону</div>
                <div class="notif-desc">Получать email при автоматическом создании счёта (и акта, если он предусмотрен шаблоном) по расписанию умного счёта</div>
              </div>
              <button class="toggle {{ $notifyInvoiceFromTemplate ? 'on' : '' }}" data-toggle="notif-invoice-template"></button>
            </div>
          </div>
          <div class="sc-foot">
            <button class="btn btn-primary" id="notif-save-btn">Сохранить</button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="toast" id="toast">
  <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
    <circle cx="8" cy="8" r="7" fill="#159B6A"/>
    <path d="M5 8l2.5 2.5L11 5.5" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>
  <span id="toast-text">Сохранено</span>
</div>
@endsection

@push('scripts')
<script>
  /* Tabs */
  function activateTab(tabId) {
    const btn   = document.querySelector(`.tab-link[data-tab="${tabId}"]`);
    const panel = document.getElementById(tabId);
    if (!btn || !panel) return;
    document.querySelectorAll('.tab-link').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    panel.classList.add('active');
  }

  document.querySelectorAll('.tab-link').forEach(btn => {
    btn.addEventListener('click', () => {
      history.replaceState(null, '', '#' + btn.dataset.tab);
      activateTab(btn.dataset.tab);
    });
  });

  // Открыть вкладку по хэшу URL (например #tab-bank)
  const initTab = window.location.hash.slice(1);
  if (initTab && document.getElementById(initTab)) activateTab(initTab);

  /* Bank accordion — делегирование на весь bank-list */
  document.getElementById('bank-list').addEventListener('click', function(e) {
    const head = e.target.closest('.bank-item-head');
    if (!head) return;
    const body = document.getElementById('bank-body-' + head.dataset.bank);
    if (!body) return;
    body.classList.toggle('open');
    head.querySelector('.bank-chevron').style.transform =
      body.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0deg)';
  });

  /* Delete — делегирование для серверных и динамических карточек */
  document.getElementById('bank-list').addEventListener('click', async function(e) {
    const btn = e.target.closest('.btn-delete-bank');
    if (!btn) return;
    const id   = btn.dataset.id;
    const card = document.getElementById('bank-card-' + id);
    const name = card?.querySelector('.bank-item-name')?.textContent ?? '';
    if (!confirm('Удалить банковский счёт «' + name + '»?')) return;
    btn.disabled = true;
    try {
      const res = await fetch(bankDestroyBaseUrl + '/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
      });
      if (res.ok) { card?.remove(); showToast('Счёт удалён'); }
      else { showToast('Не удалось удалить счёт'); btn.disabled = false; }
    } catch {
      showToast('Ошибка соединения'); btn.disabled = false;
    }
  });

  /* Password eye toggle */
  document.querySelectorAll('.pw-eye').forEach(btn => {
    btn.addEventListener('click', () => {
      const inp = document.getElementById(btn.dataset.target);
      inp.type = inp.type === 'password' ? 'text' : 'password';
      btn.style.color = inp.type === 'text' ? 'var(--accent)' : '';
    });
  });

  /* Password strength */
  document.getElementById('pw-new').addEventListener('input', function() {
    const v = this.value;
    const bars = [document.getElementById('bar1'), document.getElementById('bar2'), document.getElementById('bar3')];
    const lbl = document.getElementById('pw-lbl');
    if (!v) { bars.forEach(b => b.className = 'pw-bar'); lbl.textContent = ''; return; }
    const s   = v.length < 5 ? 1 : v.length < 8 ? 2 : 3;
    const cls = s === 1 ? 'weak' : s === 2 ? 'medium' : 'strong';
    const lbts = { weak: 'Слабый', medium: 'Средний', strong: 'Надёжный' };
    bars.forEach((b, i) => { b.className = 'pw-bar' + (i < s ? ' ' + cls : ''); });
    lbl.className = 'pw-label ' + cls;
    lbl.textContent = lbts[cls] + ' пароль';
  });

  /* Notification toggles */
  document.querySelectorAll('.toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      if (btn.dataset.toggle === 'notif-invoice-template' && !btn.classList.contains('on')) {
        const email = document.getElementById('profile-email')?.value.trim();
        if (!email) {
          showToast('Сначала укажите email в профиле — иначе отправлять уведомление некуда');
          return;
        }
      }
      btn.classList.toggle('on');
    });
  });

  /* Notification settings save */
  document.getElementById('notif-save-btn').addEventListener('click', async function() {
    const enabled = document.querySelector('[data-toggle="notif-invoice-template"]').classList.contains('on');

    const origHtml = this.innerHTML;
    this.textContent = 'Сохраняем…';
    this.disabled = true;

    try {
      const res = await fetch('{{ route('cabinet.notifications.save') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ notify_invoice_from_template: enabled }),
      });

      const json = await res.json();
      showToast(res.ok ? 'Настройки уведомлений сохранены' : (json.error ?? 'Ошибка сохранения'));
    } catch {
      showToast('Ошибка соединения');
    } finally {
      this.innerHTML = origHtml;
      this.disabled = false;
    }
  });

  /* Save buttons */
  document.getElementById('org-save').addEventListener('click', async function() {
    clearOrgErrors();

    const inn = document.getElementById('org-inn').value.trim();
    if (!inn) {
      setFieldError('org-inn', true);
      showToast('Введите ИНН');
      return;
    }
    if (!/^\d{10}$|^\d{12}$/.test(inn)) {
      setFieldError('org-inn', true);
      showToast('ИНН — 10 цифр (юр. лицо) или 12 цифр (ИП)');
      return;
    }

    const origHtml = this.innerHTML;
    this.textContent = 'Сохраняем…';
    this.disabled = true;

    try {
      const res = await fetch('{{ route('cabinet.requisites.save') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
          inn:     inn,
          name:    document.getElementById('org-name').value.trim(),
          short:   document.getElementById('org-short').value.trim(),
          ogrn:    document.getElementById('org-ogrn').value.trim(),
          kpp:     document.getElementById('org-kpp').value.trim(),
          address: document.getElementById('org-addr').value.trim(),
          phone:   document.getElementById('org-phone')._iMask?.unmaskedValue,
          email:   document.getElementById('org-email').value.trim(),
        }),
      });

      const json = await res.json();

      if (!res.ok) {
        if (json.errors) {
          Object.keys(json.errors).forEach(key => {
            if (orgFieldMap[key]) setFieldError(orgFieldMap[key], true);
          });
          const first = Object.values(json.errors)[0];
          showToast(Array.isArray(first) ? first[0] : first);
        } else {
          showToast(json.error ?? 'Ошибка сохранения');
        }
      } else {
        const short = document.getElementById('org-short').value.trim();
        const name  = document.getElementById('org-name').value.trim();
        document.getElementById('fns-chip-text').innerHTML =
          'Реквизиты сохранены: <strong>' + escHtml(short || name) + '</strong>';
        document.getElementById('fns-chip').style.display = 'flex';
        clearOrgErrors();
        showToast('Реквизиты сохранены');
      }
    } catch {
      showToast('Ошибка соединения');
    } finally {
      this.innerHTML = origHtml;
      this.disabled = false;
    }
  });

  document.getElementById('org-cancel').addEventListener('click', () => showToast('Изменения отменены'));
  document.getElementById('inn-refresh-btn').addEventListener('click', async function() {
    const inn = document.getElementById('org-inn').value.trim();
    if (!inn) { showToast('Введите ИНН'); return; }

    const origHtml = this.innerHTML;
    this.textContent = 'Обновляем…';
    this.disabled = true;

    try {
      const res = await fetch('{{ route('dadata.party') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ inn }),
      });

      const json = await res.json();

      if (!res.ok) {
        showToast(json.error ?? 'Организация не найдена');
      } else {
        document.getElementById('org-name').value  = json.name;
        document.getElementById('org-short').value = json.short;
        document.getElementById('org-ogrn').value  = json.ogrn;
        document.getElementById('org-kpp').value   = json.kpp;
        document.getElementById('org-addr').value  = json.address;
        if (json.email) document.getElementById('org-email').value = json.email;
        if (json.phone) { const m = document.getElementById('org-phone')._iMask; if (m) m.unmaskedValue = json.phone; }

        const chipText = document.getElementById('fns-chip-text');
        chipText.innerHTML = 'Данные получены: <strong>' + escHtml(json.short || json.name) + '</strong>';
        document.getElementById('fns-chip').style.display = 'flex';

        showToast('Реквизиты заполнены из ФНС');
      }
    } catch {
      showToast('Ошибка соединения');
    } finally {
      this.innerHTML = origHtml;
      this.disabled = false;
    }
  });

  const addBankBtn  = document.getElementById('add-bank-btn');
  const addBankForm = document.getElementById('bank-add-form');
  const addBankCancel = document.getElementById('add-bank-cancel');

  addBankBtn.addEventListener('click', () => {
    addBankForm.style.display = 'block';
    addBankBtn.style.display = 'none';
    document.getElementById('new-bank-name').focus();
  });

  addBankCancel.addEventListener('click', () => {
    addBankForm.style.display = 'none';
    addBankBtn.style.display = 'flex';
    addBankForm.querySelectorAll('.field-input').forEach(i => i.value = '');
    clearBankErrors();
  });

  document.getElementById('bik-lookup-btn').addEventListener('click', async function() {
    const bik = document.getElementById('new-bank-bik').value.trim();
    if (bik.length !== 9) { showToast('Введите 9-значный БИК'); return; }

    const btnHtml = this.innerHTML;
    this.textContent = 'Ищем…';
    this.disabled = true;

    try {
      const res = await fetch('{{ route('dadata.bank') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ bik }),
      });

      const json = await res.json();

      if (!res.ok) {
        showToast(json.error ?? 'Банк не найден');
      } else {
        document.getElementById('new-bank-name').value = json.name;
        document.getElementById('new-bank-corr').value = json.correspondent_account;
        document.getElementById('new-bank-city').value = json.city;
        showToast('Данные банка получены');
      }
    } catch {
      showToast('Ошибка соединения');
    } finally {
      this.innerHTML = btnHtml;
      this.disabled = false;
    }
  });

  const bankDestroyBaseUrl  = '{{ url('/cabinet/bank-accounts') }}';
  const bankReorderUrl      = '{{ route('cabinet.bank-accounts.reorder') }}';
  const CSRF                = document.querySelector('meta[name="csrf-token"]').content;

  /* ── Drag & Drop ── */
  const bankSort = makeSortable({
    getContainer: () => document.getElementById('bank-list'),
    itemSel:      '.bank-item',
    handleSel:    '.bank-drag-handle',
    saveUrl:      bankReorderUrl,
    csrf:         CSRF,
    getId:        el => parseInt(el.id.replace('bank-card-', '')),
    onDragEnd:    updatePrimaryVisual,
  });

  document.querySelectorAll('#bank-list .bank-item').forEach(bankSort.attach);

  function updatePrimaryVisual() {
    document.querySelectorAll('#bank-list .bank-item').forEach((item, i) => {
      const isPrimary = i === 0;
      item.classList.toggle('primary', isPrimary);
      const head  = item.querySelector('.bank-item-head');
      const badge = head.querySelector('.bank-primary-badge');
      if (isPrimary && !badge) {
        const span = document.createElement('span');
        span.className   = 'bank-primary-badge';
        span.textContent = 'Основной';
        head.querySelector('.bank-chevron').before(span);
      } else if (!isPrimary && badge) {
        badge.remove();
      }
    });
  }

  function escHtml(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function createBankCard(account) {
    const el = document.createElement('div');
    el.className = 'bank-item' + (account.is_primary ? ' primary' : '');
    el.id = 'bank-card-' + account.id;

    const ico = `<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="14" height="10" rx="1.5"/><path d="M1 8h14M5 11.5h2.5"/></svg>`;

    const badge = account.is_primary ? `<span class="bank-primary-badge">Основной</span>` : '';

    el.innerHTML = `
      <div class="bank-item-head" data-bank="${account.id}">
        <div class="bank-drag-handle" title="Перетащить"><svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor"><circle cx="3" cy="2.5" r="1.2"/><circle cx="7" cy="2.5" r="1.2"/><circle cx="3" cy="7" r="1.2"/><circle cx="7" cy="7" r="1.2"/><circle cx="3" cy="11.5" r="1.2"/><circle cx="7" cy="11.5" r="1.2"/></svg></div>
        <div class="bank-item-head-ico">${ico}</div>
        <div class="bank-item-head-info">
          <div class="bank-item-name">${escHtml(account.bank)}</div>
          <div class="bank-item-acc">${escHtml(account.payment_account)}</div>
        </div>
        ${badge}
        <svg class="bank-chevron" width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="transition:transform .2s;margin-left:auto;flex-shrink:0;transform:rotate(180deg)"><path d="M3.5 5.5l3.5 3.5 3.5-3.5"/></svg>
      </div>
      <div class="bank-collapse open" id="bank-body-${account.id}">
        <div class="bank-item-body">
          <div class="bank-fields">
            <div class="field">
              <div class="field-label">БИК</div>
              <input class="field-input" type="text" readonly value="${escHtml(account.bik)}">
            </div>
            <div class="field">
              <div class="field-label">Расчётный счёт</div>
              <input class="field-input" type="text" readonly value="${escHtml(account.payment_account)}">
            </div>
            <div class="field">
              <div class="field-label">Корреспондентский счёт</div>
              <input class="field-input" type="text" readonly value="${escHtml(account.correspondent_account)}">
            </div>
            <div class="field">
              <div class="field-label">Город</div>
              <input class="field-input" type="text" readonly value="${escHtml(account.city)}">
            </div>
          </div>
        </div>
        <div class="bank-item-foot">
          <span></span>
          <button class="btn btn-danger btn-delete-bank" data-id="${account.id}" style="font-size:12.5px;padding:7px 14px;">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3.5h9M5 3.5v-1h3v1M10 3.5l-.7 7H3.7l-.7-7"/></svg>
            Удалить
          </button>
        </div>
      </div>`;

    return el;
  }

  const bankFieldMap = {
    bik:                   'new-bank-bik',
    bank:                  'new-bank-name',
    payment_account:       'new-bank-payment',
    correspondent_account: 'new-bank-corr',
    city:                  'new-bank-city',
  };

  function setFieldError(id, isError) {
    const el = document.getElementById(id);
    if (el) el.classList.toggle('error', isError);
  }

  function clearErrors(fieldMap) {
    Object.values(fieldMap).forEach(id => setFieldError(id, false));
  }

  function bindErrorClear(fieldMap) {
    Object.values(fieldMap).forEach(id => {
      const el = document.getElementById(id);
      if (el) el.addEventListener('input', function() { this.classList.remove('error'); });
    });
  }

  /* Bank fields */
  function clearBankErrors() { clearErrors(bankFieldMap); }
  bindErrorClear(bankFieldMap);

  /* Org fields */
  const orgFieldMap = {
    inn:     'org-inn',
    name:    'org-name',
    short:   'org-short',
    ogrn:    'org-ogrn',
    kpp:     'org-kpp',
    address: 'org-addr',
    phone:   'org-phone',
    email:   'org-email',
  };
  function clearOrgErrors() { clearErrors(orgFieldMap); }
  bindErrorClear(orgFieldMap);

  document.getElementById('add-bank-save').addEventListener('click', async function() {
    const bik  = document.getElementById('new-bank-bik').value.trim();
    const bank = document.getElementById('new-bank-name').value.trim();
    const pay  = document.getElementById('new-bank-payment').value.trim();
    const corr = document.getElementById('new-bank-corr').value.trim();
    const city = document.getElementById('new-bank-city').value.trim();

    clearBankErrors();

    if (!bik || bik.length !== 9)   { setFieldError('new-bank-bik', true);     showToast('Введите 9-значный БИК'); return; }
    if (!bank)                       { setFieldError('new-bank-name', true);    showToast('Введите наименование банка'); return; }
    if (!pay  || pay.length  !== 20) { setFieldError('new-bank-payment', true); showToast('Расчётный счёт — 20 цифр'); return; }
    if (!corr || corr.length !== 20) { setFieldError('new-bank-corr', true);    showToast('Корреспондентский счёт — 20 цифр'); return; }
    if (!city)                       { setFieldError('new-bank-city', true);    showToast('Введите город'); return; }

    const origHtml = this.innerHTML;
    this.textContent = 'Сохраняем…';
    this.disabled = true;

    try {
      const res = await fetch('{{ route('cabinet.bank-accounts.store') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ bik, bank, payment_account: pay, correspondent_account: corr, city }),
      });

      const json = await res.json();

      if (!res.ok) {
        if (json.errors) {
          Object.keys(json.errors).forEach(key => {
            if (bankFieldMap[key]) setFieldError(bankFieldMap[key], true);
          });
          const first = Object.values(json.errors)[0];
          showToast(Array.isArray(first) ? first[0] : first);
        } else {
          if (json.field && bankFieldMap[json.field]) setFieldError(bankFieldMap[json.field], true);
          showToast(json.error ?? 'Ошибка сохранения');
        }
      } else {
        const newCard = createBankCard(json.account);
        bankSort.attach(newCard);
        document.getElementById('bank-list').appendChild(newCard);
        showToast('Банковский счёт добавлен');
        addBankForm.style.display = 'none';
        addBankBtn.style.display = 'flex';
        addBankForm.querySelectorAll('.field-input').forEach(i => i.value = '');
        clearBankErrors();
      }
    } catch {
      showToast('Ошибка соединения');
    } finally {
      this.innerHTML = origHtml;
      this.disabled = false;
    }
  });

  /* Toast */
  let toastTimer;
  function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toast-text').textContent = msg;
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
  }
  window.showToast = showToast;

  /* Password save */
  document.getElementById('pw-save-btn').addEventListener('click', async function() {
    const pwNew = document.getElementById('pw-new');
    const pwRep = document.getElementById('pw-rep');
    pwNew.classList.remove('error');
    pwRep.classList.remove('error');

    if (pwNew.value.length < 5) {
      pwNew.classList.add('error');
      showToast('Пароль должен содержать не менее 5 символов');
      return;
    }
    if (pwNew.value !== pwRep.value) {
      pwRep.classList.add('error');
      showToast('Пароли не совпадают');
      return;
    }

    const origHtml = this.innerHTML;
    this.textContent = 'Сохраняем…';
    this.disabled = true;

    try {
      const res = await fetch('{{ route('cabinet.password.change') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
          password:              pwNew.value,
          password_confirmation: pwRep.value,
        }),
      });

      const json = await res.json();

      if (!res.ok) {
        if (json.errors?.password) {
          pwNew.classList.add('error');
          showToast(Array.isArray(json.errors.password) ? json.errors.password[0] : json.errors.password);
        } else {
          showToast(json.error ?? 'Ошибка сохранения');
        }
      } else {
        pwNew.value = '';
        pwRep.value = '';
        ['bar1','bar2','bar3'].forEach(id => document.getElementById(id).className = 'pw-bar');
        document.getElementById('pw-lbl').textContent = '';
        showToast('Пароль успешно изменён');
      }
    } catch {
      showToast('Ошибка соединения');
    } finally {
      this.innerHTML = origHtml;
      this.disabled = false;
    }
  });


  /* Profile save */
  const profileFieldMap = {
    name:  'profile-name',
    phone: 'profile-phone',
    email: 'profile-email',
  };
  bindErrorClear(profileFieldMap);

  document.getElementById('profile-save-btn').addEventListener('click', async function() {
    Object.values(profileFieldMap).forEach(id => setFieldError(id, false));

    const name  = document.getElementById('profile-name').value.trim();
    const phone = document.getElementById('profile-phone')._iMask?.unmaskedValue;
    const email = document.getElementById('profile-email').value.trim();

    if (!name)  { setFieldError('profile-name',  true); showToast('Введите имя'); return; }
    if (!email) { setFieldError('profile-email', true); showToast('Введите email'); return; }

    const origHtml = this.innerHTML;
    this.textContent = 'Сохраняем…';
    this.disabled = true;

    try {
      const res = await fetch('{{ route('cabinet.profile.save') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ name, phone, email }),
      });

      const json = await res.json();

      if (!res.ok) {
        if (json.errors) {
          Object.keys(json.errors).forEach(key => {
            if (profileFieldMap[key]) setFieldError(profileFieldMap[key], true);
          });
          const first = Object.values(json.errors)[0];
          showToast(Array.isArray(first) ? first[0] : first);
        } else {
          showToast(json.error ?? 'Ошибка сохранения');
        }
      } else {
        showToast('Личные данные сохранены');
      }
    } catch {
      showToast('Ошибка соединения');
    } finally {
      this.innerHTML = origHtml;
      this.disabled = false;
    }
  });

  /* Account delete */
  const dzDeleteBtn = document.getElementById('dz-delete-btn');
  if (dzDeleteBtn) {
    dzDeleteBtn.addEventListener('click', async function() {
      if (!confirm('Вы уверены, что хотите удалить аккаунт? Это действие нельзя отменить.')) return;

      this.disabled = true;

      try {
        const res = await fetch('{{ route('cabinet.account.delete') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          },
        });

        const json = await res.json();

        if (res.ok) {
          document.getElementById('dz-container').innerHTML = `
            <div class="dz-title" style="color:var(--text-s)">Аккаунт помечен на удаление</div>
            <div class="dz-desc">Ваш запрос на удаление аккаунта принят. Все данные будут удалены после обработки администратором.</div>
          `;
          showToast('Запрос на удаление аккаунта отправлен');
        } else {
          showToast(json.error ?? 'Ошибка');
          this.disabled = false;
        }
      } catch {
        showToast('Ошибка соединения');
        this.disabled = false;
      }
    });
  }
</script>
@endpush
