@extends('layouts.auth')

<x-seo.meta
    title="Вход"
    description="Войдите в аккаунт СчётОк, чтобы создавать счета для ИП и самозанятых быстро и без ошибок."
/>

@section('nav-hint')
  Нет аккаунта? <a href="{{ route('register') }}">Зарегистрироваться</a>
@endsection

@section('content')
<div class="card card--login">

  <!-- Левая панель -->
  <div class="panel panel--login">
    <a href="{{ route('home') }}" class="panel-logo panel-logo--login">
      <div class="panel-logo-icon panel-logo-icon--login">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
          <rect x="1.5" y="1" width="9" height="12" rx="1.5" stroke="#fff" stroke-width="1.3"/>
          <path d="M8.5 1v3.5H12" stroke="#fff" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M3.5 6.5h6M3.5 9h6M3.5 11.5h3.5" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/>
        </svg>
      </div>
      СчётОк
    </a>
    <div class="panel-h panel-h--login">С возвращением!</div>
    <p class="panel-sub panel-sub--login">Войдите в аккаунт и выставьте счёт за два клика.</p>

    <!-- Декоративный счёт -->
    <div class="panel-mock">
      <div class="pm-row">
        <span class="pm-title">СЧЁТ № 247</span>
        <span class="pm-badge">PDF</span>
      </div>
      <div class="pm-line"></div>
      <div class="pm-field"><span>Поставщик</span><span>ООО «Альфа»</span></div>
      <div class="pm-field"><span>Покупатель</span><span>ИП Смирнов</span></div>
      <div class="pm-field"><span>Дата</span><span>{{ date("d.m.Y") }}</span></div>
      <div class="pm-total"><span>Итого</span><span>50 000 ₽</span></div>
      <div class="pm-btns">
        <button class="pm-btn dl">↓ Скачать PDF</button>
        <button class="pm-btn em">✉ Email</button>
      </div>
    </div>

    <div class="panel-footer panel-footer--login">
      Ваши данные защищены. Доступ только по паролю.
    </div>
  </div>

  <!-- Правая форма -->
  <div class="form-panel form-panel--login">
    <div class="form-eyebrow">Вход</div>
    <div class="form-title form-title--login">Добро пожаловать</div>
    <p class="form-sub">
      Нет аккаунта? <a href="{{ route('register') }}">Зарегистрироваться</a>
    </p>

    <!-- JS-валидация -->
    <div class="alert" id="alert">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
        <circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.4"/>
        <path d="M8 5v3.5M8 10.5v.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
      </svg>
      <span id="alert-text"></span>
    </div>

    <form class="fields" id="login-form" action="{{ route('login') }}" method="POST" novalidate>
      @csrf

      <!-- Email -->
      <div class="field">
        <label class="field-label" for="f-email">Электронная почта</label>
        <div class="input-wrap">
          <input class="field-input"
            id="f-email" name="email" type="email"
            placeholder="alex@company.ru" autocomplete="email"
            value="{{ old('email') }}">
          <span class="field-icon" id="icon-email"></span>
        </div>
        <span class="field-msg" id="msg-email"></span>
      </div>

      <!-- Пароль -->
      <div class="field">
        <label class="field-label" for="f-pw">
          Пароль
          <a href="#" tabindex="-1">Забыли пароль?</a>
        </label>
        <div class="input-wrap">
          <input class="field-input {{ $errors->has('password') ? 'invalid' : '' }}"
            id="f-pw" name="password" type="password"
            placeholder="Введите пароль" autocomplete="current-password">
          <button type="button" class="pw-toggle" id="pw-toggle" tabindex="-1" aria-label="Показать пароль">
            <svg id="eye-open" width="18" height="18" viewBox="0 0 18 18" fill="none">
              <path d="M1.5 9s2.5-5 7.5-5 7.5 5 7.5 5-2.5 5-7.5 5-7.5-5-7.5-5z" stroke="currentColor" stroke-width="1.4"/>
              <circle cx="9" cy="9" r="2" stroke="currentColor" stroke-width="1.4"/>
            </svg>
            <svg id="eye-closed" width="18" height="18" viewBox="0 0 18 18" fill="none" style="display:none">
              <path d="M2 3l14 12M7.5 5.2A7.5 7.5 0 0 1 9 5c5 0 7.5 4 7.5 4s-.9 1.7-2.5 3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
              <path d="M4 7.2C2.7 8.2 1.5 9 1.5 9s2.5 5 7.5 5c1.4 0 2.6-.4 3.6-1" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
            </svg>
          </button>
        </div>
        <span class="field-msg" id="msg-pw"></span>
      </div>

      <!-- Remember me -->
      <label style="display:flex;align-items:center;gap:9px;cursor:pointer;font-size:13.5px;color:var(--text-b);margin-bottom:4px;">
        <input type="checkbox" name="remember" value="1" checked
          style="width:16px;height:16px;accent-color:var(--accent);cursor:pointer;flex-shrink:0;">
        Запомнить меня
      </label>

      <!-- Submit -->
      <button type="submit" class="submit-btn" id="submit-btn">
        <span class="spinner" id="spinner"></span>
        <span class="btn-label">Войти</span>
      </button>

      <div class="divider">или</div>

      <div class="reg-link">
        Первый раз здесь? <a href="{{ route('register') }}">Создать аккаунт</a>
      </div>

    </form>
  </div>

</div>
@endsection

@push('scripts')
<script>
  const svgOk = `<svg class="field-icon ok" width="16" height="16" viewBox="0 0 16 16" fill="none">
    <circle cx="8" cy="8" r="7" fill="#E6F7EF"/>
    <path d="M5 8l2.5 2.5L11 5.5" stroke="#159B6A" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>`;
  const svgErr = `<svg class="field-icon err" width="16" height="16" viewBox="0 0 16 16" fill="none">
    <circle cx="8" cy="8" r="7" fill="#FEF0F0"/>
    <path d="M10.5 5.5l-5 5M5.5 5.5l5 5" stroke="#D63B3B" stroke-width="1.6" stroke-linecap="round"/>
  </svg>`;

  const fEmail = document.getElementById('f-email');
  const iEmail = document.getElementById('icon-email');
  const mEmail = document.getElementById('msg-email');
  const fPw    = document.getElementById('f-pw');
  const mPw    = document.getElementById('msg-pw');
  const alert  = document.getElementById('alert');

  function setState(input, iconEl, msgEl, state, msg) {
    input.classList.remove('valid', 'invalid');
    if (state === 'ok')  { input.classList.add('valid');   if (iconEl) iconEl.innerHTML = svgOk; }
    if (state === 'err') { input.classList.add('invalid'); if (iconEl) iconEl.innerHTML = svgErr; }
    if (state === '')    { if (iconEl) iconEl.innerHTML = ''; }
    msgEl.className = 'field-msg ' + (state === 'err' ? 'err' : '');
    msgEl.textContent = msg || '';
  }

  function validateEmail() {
    const v = fEmail.value.trim();
    if (!v) { setState(fEmail, iEmail, mEmail, 'err', 'Введите электронную почту'); return false; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v)) { setState(fEmail, iEmail, mEmail, 'err', 'Некорректный адрес'); return false; }
    setState(fEmail, iEmail, mEmail, 'ok', ''); return true;
  }

  function validatePw() {
    const v = fPw.value;
    if (!v) { setState(fPw, null, mPw, 'err', 'Введите пароль'); return false; }
    setState(fPw, null, mPw, '', ''); return true;
  }

  fEmail.addEventListener('blur', validateEmail);
  fEmail.addEventListener('input', () => {
    if (fEmail.classList.contains('invalid')) validateEmail();
    alert.classList.remove('show');
  });
  fPw.addEventListener('blur', validatePw);
  fPw.addEventListener('input', () => {
    if (fPw.classList.contains('invalid')) validatePw();
    alert.classList.remove('show');
  });

  /* Password toggle */
  document.getElementById('pw-toggle').addEventListener('click', function() {
    const show = fPw.type === 'password';
    fPw.type = show ? 'text' : 'password';
    document.getElementById('eye-open').style.display  = show ? 'none' : '';
    document.getElementById('eye-closed').style.display = show ? '' : 'none';
    this.style.color = show ? 'var(--accent)' : '';
  });

  /* Submit */
  document.getElementById('login-form').addEventListener('submit', function(e) {
    const eOk = validateEmail();
    const pOk = validatePw();
    if (!eOk || !pOk) { e.preventDefault(); return; }

    const btn = document.getElementById('submit-btn');
    btn.classList.add('loading');
    btn.disabled = true;
  });

  fEmail.focus();
</script>
@endpush
