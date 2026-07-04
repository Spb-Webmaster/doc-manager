@extends('layouts.layout')
<x-seo.meta
    title="{{ $home['metatitle'] }}"
    description="{{ $home['description'] }}"
    keywords="{{ $home['keywords'] }}"
/>
@section('content')

<!-- ══════════ HERO ══════════ -->
<section class="hero">
  <div class="hero-content">
    <div class="hero-tag">
      <svg width="8" height="8" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4" fill="currentColor"/></svg>
      Для ИП и самозанятых
    </div>
    <h1 class="hero-h1">Создать счёт?<br><em>Два клика.</em></h1>
    <p class="hero-sub">
      Введите ИНН — реквизиты заполнятся автоматически из реестра ФНС.
      Выберите дату — PDF счёт готов к отправке или скачиванию.
    </p>
    <div class="hero-actions">
      @guest
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Создать счёт бесплатно</a>
        <a href="{{ route('login') }}" class="btn btn-ghost btn-lg">Войти</a>
      @else
        <a href="{{ route('cabinet.invoices.create') }}" class="btn btn-primary btn-lg">Создать счёт</a>
      @endguest
    </div>
    <div class="hero-note">
      <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
        <path d="M2.5 7l3 3 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Регистрация бесплатна — без банковской карты
    </div>
  </div>

  <!-- Mini product mock -->
  <div class="hero-visual" aria-hidden="true">
    <div class="mock-card">
      <div class="mc-top">
        <div class="mc-top-dot"></div>
        <span class="mc-top-label">Новый счёт</span>
      </div>
      <div class="mc-row">
        <div class="mc-label">Ваш ИНН</div>
        <div class="mc-input">7712 345678</div>
        <div class="mc-filled">
          <svg width="11" height="11" viewBox="0 0 11 11" fill="none">
            <path d="M2 5.5l2.5 2.5 4.5-4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          ООО «Альфа Трейд»
        </div>
      </div>
      <div class="mc-row">
        <div class="mc-label">ИНН контрагента</div>
        <div class="mc-input">5012 345678</div>
        <div class="mc-filled">
          <svg width="11" height="11" viewBox="0 0 11 11" fill="none">
            <path d="M2 5.5l2.5 2.5 4.5-4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          ИП Смирнов А.В.
        </div>
      </div>
      <div class="mc-row">
        <div class="mc-label">Дата счёта</div>
        <div class="mc-date">
          <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
            <rect x="1" y="2" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/>
            <path d="M4 1v2M8 1v2M1 5h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
          </svg>
          {{ now()->locale('ru')->isoFormat('D MMMM YYYY') }}
        </div>
      </div>
      <a href="{{ route('cabinet.invoices.create') }}" class="mc-btn">Создать счёт →</a>
    </div>

    <div class="mock-arrow">
      <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
        <path d="M6 14h16M17 9l5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>

    <div class="mock-pdf">
      <div class="mp-badge">PDF</div>
      <div class="mp-title">СЧЁТ № 128</div>
      <div class="mp-date">от {{ now()->format('d.m.Y') }}</div>
      <div class="mp-row"><span>Поставщик:</span><span>ООО «Альфа»</span></div>
      <div class="mp-row"><span>Покупатель:</span><span>ИП Смирнов</span></div>
      <div class="mp-table">
        <div class="mp-trow head"><span>Услуга</span><span>Сумма</span></div>
        <div class="mp-trow"><span>Консалтинг</span><span>50 000 ₽</span></div>
      </div>
      <div class="mp-total">Итого: 50 000 ₽</div>
      <div class="mp-actions">
        <button class="mp-btn dl">↓ PDF</button>
        <button class="mp-btn em">✉ Email</button>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ TRUST BAR ══════════ -->
<div class="trust">
  <div class="wrap trust-inner">
    <div class="trust-item">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
        <path d="M8 1.5l1.8 4.3 4.7.4-3.4 3 1 4.6L8 11.5l-4.1 2.3 1-4.6-3.4-3 4.7-.4z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
      </svg>
      Данные из реестра ФНС
    </div>
    <div class="trust-sep"></div>
    <div class="trust-item">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
        <path d="M10 2H4a1.5 1.5 0 0 0-1.5 1.5v9A1.5 1.5 0 0 0 4 14h8a1.5 1.5 0 0 0 1.5-1.5V5.5z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
        <path d="M10 2v3.5H13.5M5.5 7.5h5M5.5 10h5M5.5 12.5h3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
      </svg>
      Счёт в формате PDF
    </div>
    <div class="trust-sep"></div>
    <div class="trust-item">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
        <rect x="1.5" y="3.5" width="13" height="9" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
        <path d="M1.5 6l6.5 4 6.5-4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
      </svg>
      Отправка по email
    </div>
    <div class="trust-sep"></div>
    <div class="trust-item">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
        <path d="M8 1.5C5.5 1.5 3.5 3.5 3.5 6c0 3.5 4.5 8.5 4.5 8.5s4.5-5 4.5-8.5c0-2.5-2-4.5-4.5-4.5z" stroke="currentColor" stroke-width="1.3"/>
        <circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.3"/>
      </svg>
      Без ошибок в реквизитах
    </div>
  </div>
</div>

<!-- ══════════ HOW IT WORKS ══════════ -->
<section class="sec how">
  <div class="sec-inner">
    <div class="sec-head fi">
      <div class="sec-tag">Как это работает</div>
      <h2 class="sec-title">Три шага — и счёт готов</h2>
      <p class="sec-sub">Никаких сложных форм, ручного ввода и ошибок в реквизитах.</p>
    </div>
    <div class="steps">
      <div class="step fi">
        <div class="step-num">1</div>
        <div class="step-title">Введите свой ИНН</div>
        <p class="step-desc">
          Система автоматически запрашивает данные из реестра ФНС — название организации, адрес, КПП и ОГРН.

        </p>
        <div class="step-badge">
          <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
            <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Автозаполнение из ФНС
        </div>
      </div>
      <div class="step-conn fi">
        <svg width="26" height="26" viewBox="0 0 26 26" fill="none">
          <path d="M5 13h16M16 8l5 5-5 5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="step fi" style="transition-delay:.1s">
        <div class="step-num">2</div>
        <div class="step-title">ИНН контрагента и дата</div>
        <p class="step-desc">
          Данные покупателя тоже подтягиваются из ФНС автоматически.
          Выберите дату счёта через удобный встроенный календарь.
        </p>
        <div class="step-badge">
          <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
            <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Выбор даты через календарь
        </div>
      </div>
      <div class="step-conn fi" style="transition-delay:.2s">
        <svg width="26" height="26" viewBox="0 0 26 26" fill="none">
          <path d="M5 13h16M16 8l5 5-5 5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="step fi" style="transition-delay:.2s">
        <div class="step-num">3</div>
        <div class="step-title">Скачайте PDF или отправьте</div>
        <p class="step-desc">
          Счёт формируется мгновенно в стандартном виде. Скачайте PDF одним кликом
          или отправьте напрямую контрагенту по email.
        </p>
        <div class="step-badge">
          <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
            <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          PDF за секунду
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ BENEFITS ══════════ -->
<section class="sec">
  <div class="sec-inner">
    <div class="sec-head fi">
      <div class="sec-tag">Преимущества</div>
      <h2 class="sec-title">Почему выбирают СчётОк</h2>
      <p class="sec-sub">Создан специально для ИП и самозанятых — минимум действий, максимум результата.</p>
    </div>
    <div class="ben-grid">
      <div class="ben-card fi">
        <div class="ben-icon" style="background:#EAF0FD;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2550E2" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <path d="M14 2v6h6M8 13h8M8 17h5"/>
          </svg>
        </div>
        <div class="ben-title">Автозаполнение реквизитов</div>
        <p class="ben-desc">Только ИНН — всё остальное сервис заполнит сам. Название, адрес, ИНН, КПП, ОГРН из официального реестра ФНС без ошибок.</p>
      </div>
      <div class="ben-card fi" style="transition-delay:.07s">
        <div class="ben-icon" style="background:#E6F7EF;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#159B6A" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <path d="M16 2v4M8 2v4M3 10h18M8 14h2M11 14h5M8 18h5"/>
          </svg>
        </div>
        <div class="ben-title">Удобный выбор дат</div>
        <p class="ben-desc">Встроенный календарь для быстрого выбора периода. Несколько кликов — и дата указана точно, без ручного ввода.</p>
      </div>
      <div class="ben-card fi" style="transition-delay:.14s">
        <div class="ben-icon" style="background:#FFF3E0;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C96B00" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <path d="M14 2v6h6M12 18v-5M9.5 15.5l2.5 2.5 2.5-2.5"/>
          </svg>
        </div>
        <div class="ben-title">PDF одним кликом</div>
        <p class="ben-desc">Счёт формируется мгновенно в стандартном формате. Скачайте готовый документ без лишних шагов и настроек.</p>
      </div>
      <div class="ben-card fi" style="transition-delay:.21s">
        <div class="ben-icon" style="background:#F0ECFE;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6B45D8" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="4" width="20" height="16" rx="2"/>
            <path d="M2 8l10 6 10-6"/>
          </svg>
        </div>
        <div class="ben-title">Отправка по email</div>
        <p class="ben-desc">Не нужно скачивать и прикреплять вручную. Отправьте счёт прямо из сервиса — контрагент получит письмо сразу.</p>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ STATS ══════════ -->
<section class="sec stats-sec">
  <div class="sec-inner">
    <div class="sec-head fi">
      <div class="sec-tag">Возможности кабинета</div>
      <h2 class="sec-title">СчётОк умеет больше, чем считать</h2>
      <p class="sec-sub">Не только счета — акты, договоры и повторяющиеся платежи под контролем.</p>
    </div>
    <div class="stats-grid">
      <div class="stat-item fi">
        <div class="feat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 2v6h-6M3 22v-6h6"/>
            <path d="M3.5 12a8.5 8.5 0 0 1 14.6-5.9L21 8M20.5 12a8.5 8.5 0 0 1-14.6 5.9L3 16"/>
          </svg>
        </div>
        <div class="stat-label"><b>Умные счета —</b><br>автосоздание по расписанию. Счёт и акт формируются и уходят контрагенту сами: раз в месяц или квартал, без ручного участия.</div>
      </div>
      <div class="stat-item fi" style="transition-delay:.1s">
        <div class="feat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <path d="M14 2v6h6M9 13l2 2 4-4"/>
          </svg>
        </div>
        <div class="stat-label"><b>Акты выполненных работ —</b><br>формируются вместе со счётом или отдельно, в том же стандартном PDF-формате.</div>
      </div>
      <div class="stat-item fi" style="transition-delay:.2s">
        <div class="feat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
          </svg>
        </div>
        <div class="stat-label"><b>Несколько договоров на контрагента —</b><br>все документы по одному партнёру в одном месте, с сортировкой по важности.</div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════ CTA ══════════ -->
<section class="cta-sec">
  <div class="cta-card fi">
    <div class="sec-tag">Начать работу</div>
    <h2 class="sec-title">Попробуйте бесплатно —<br>первый счёт за 2 минуты</h2>
    <p class="sec-sub">Регистрация занимает меньше минуты. Сразу работа — без тестовых ограничений.</p>
    <div class="cta-btns">
      @guest
        <a href="{{ route('register') }}" class="btn btn-primary btn-xl">Зарегистрироваться</a>
        <a href="{{ route('login') }}" class="btn btn-ghost btn-xl">Войти в кабинет</a>
      @else
        <a href="{{ route('cabinet.invoices.create') }}" class="btn btn-primary btn-xl">Создать счёт</a>
      @endguest
    </div>
  </div>
</section>

<!-- ══════════ TWEAKS PANEL ══════════ -->
<div id="tweaks-panel">
  <div class="tp-header">
    Tweaks
    <button class="tp-close" id="tp-close-btn">×</button>
  </div>
  <div class="tp-section-title">Акцентный цвет</div>
  <div class="tp-colors">
    <div class="tp-color active" style="background:#2550E2;" data-color="#2550E2" title="Синий"></div>
    <div class="tp-color" style="background:#1B9970;" data-color="#1B9970" title="Зелёный"></div>
    <div class="tp-color" style="background:#4A5568;" data-color="#4A5568" title="Грифельный"></div>
    <div class="tp-color" style="background:#7C3AED;" data-color="#7C3AED" title="Фиолетовый"></div>
  </div>
  <div class="tp-divider"></div>
  <div class="tp-input-label">Название сервиса</div>
  <input class="tp-input" id="tp-name-input" type="text" value="СчётОк" maxlength="24">
</div>

<script>
  // ── Scroll animations
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      e.target.classList.add('on');
      obs.unobserve(e.target);
    });
  }, { threshold: 0.18 });

  document.querySelectorAll('.fi').forEach(el => obs.observe(el));

  // ── Tweaks panel
  const TWEAK_DEFAULTS = /*EDITMODE-BEGIN*/{
    "accentColor": "#2550E2",
    "serviceName": "СчётОк"
  }/*EDITMODE-END*/;

  const panel = document.getElementById('tweaks-panel');
  const nameInput = document.getElementById('tp-name-input');

  function applyAccent(color) {
    document.documentElement.style.setProperty('--accent', color);
    document.documentElement.style.setProperty('--accent-hv', shadeColor(color, -18));
    document.querySelectorAll('.tp-color').forEach(c => {
      c.classList.toggle('active', c.dataset.color === color);
    });
    window.parent.postMessage({ type: '__edit_mode_set_keys', edits: { accentColor: color } }, '*');
  }

  function applyName(name) {
    document.querySelectorAll('.logo, .foot-logo').forEach(el => {
      const icon = el.querySelector('.logo-icon, .foot-logo-icon');
      el.textContent = name;
      if (icon) el.prepend(icon);
    });
    document.title = name + ' — счёт за два клика';
    window.parent.postMessage({ type: '__edit_mode_set_keys', edits: { serviceName: name } }, '*');
  }

  function shadeColor(hex, pct) {
    const n = parseInt(hex.replace('#',''), 16);
    const r = Math.max(0, Math.min(255, (n>>16) + pct));
    const g = Math.max(0, Math.min(255, ((n>>8)&0xff) + pct));
    const b = Math.max(0, Math.min(255, (n&0xff) + pct));
    return '#' + [r,g,b].map(x=>x.toString(16).padStart(2,'0')).join('');
  }

  document.querySelectorAll('.tp-color').forEach(el => {
    el.addEventListener('click', () => applyAccent(el.dataset.color));
  });

  nameInput.addEventListener('input', () => applyName(nameInput.value || 'СчётОк'));

  document.getElementById('tp-close-btn').addEventListener('click', () => {
    panel.classList.remove('open');
    window.parent.postMessage({ type: '__edit_mode_dismissed' }, '*');
  });

  applyAccent(TWEAK_DEFAULTS.accentColor);
  nameInput.value = TWEAK_DEFAULTS.serviceName;
  applyName(TWEAK_DEFAULTS.serviceName);

  window.addEventListener('message', e => {
    if (e.data?.type === '__activate_edit_mode')   panel.classList.add('open');
    if (e.data?.type === '__deactivate_edit_mode') panel.classList.remove('open');
  });
  window.parent.postMessage({ type: '__edit_mode_available' }, '*');
</script>

@endsection
