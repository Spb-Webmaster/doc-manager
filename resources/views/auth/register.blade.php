@extends('layouts.auth')

<x-seo.meta
    title="Регистрация"
    description="Создайте бесплатный аккаунт в СчётОк — сервисе для выставления счетов ИП и самозанятым. Регистрация занимает меньше минуты."
/>

@section('nav-hint')
  Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a>
@endsection

@section('content')
<div class="card card--register">

  <!-- Левая панель -->
  <div class="panel">
    <a href="{{ route('home') }}" class="panel-logo">
      <div class="panel-logo-icon">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
          <rect x="2" y="1" width="9" height="12" rx="1.5" stroke="#fff" stroke-width="1.3"/>
          <path d="M9 1v3.5h3.5" stroke="#fff" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M4 6.5h6M4 9h6M4 11.5h3.5" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/>
        </svg>
      </div>
      СчётОк
    </a>
    <div class="panel-h">Создавайте счета за&nbsp;два клика</div>
    <p class="panel-sub">Введите ИНН — реквизиты заполнятся автоматически. PDF готов к отправке.</p>
    <div class="panel-perks">
      <div class="perk">
        <div class="perk-icon">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M13 3H3a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1z"/>
            <path d="M5 7h6M5 10h4"/>
          </svg>
        </div>
        <div class="perk-text">
          <div class="perk-title">Данные из ФНС</div>
          <div class="perk-desc">Реквизиты заполняются автоматически по ИНН</div>
        </div>
      </div>
      <div class="perk">
        <div class="perk-icon">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="2" width="12" height="12" rx="2"/>
            <path d="M10 1v3M6 1v3M2 6h12M6 9h1M9 9h1M6 12h1M9 12h1"/>
          </svg>
        </div>
        <div class="perk-text">
          <div class="perk-title">Выбор дат через календарь</div>
          <div class="perk-desc">Укажите период — счёт сформируется мгновенно</div>
        </div>
      </div>
      <div class="perk">
        <div class="perk-icon">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="1" y="3" width="14" height="10" rx="1.5"/>
            <path d="M1 6.5l7 4 7-4"/>
          </svg>
        </div>
        <div class="perk-text">
          <div class="perk-title">PDF и отправка по email</div>
          <div class="perk-desc">Скачайте или отправьте контрагенту в один клик</div>
        </div>
      </div>
    </div>
    <div class="panel-footer">
      Регистрируясь, вы соглашаетесь с условиями использования сервиса СчётОк
    </div>
  </div>

  <!-- Правая форма -->
  <div class="form-panel">
    <div id="form-content">
      <div class="form-title">Регистрация</div>
      <p class="form-sub">
        Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a>
      </p>

      @if ($errors->any())
        <div class="server-errors">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form class="fields" id="reg-form" action="{{ route('register') }}" method="POST" novalidate>
        @csrf

        <!-- Имя + Телефон -->
        <div class="field-row">
          <div class="field">
            <label class="field-label" for="f-name">Имя <span>*</span></label>
            <div class="input-wrap">
              <input class="field-input has-icon {{ $errors->has('name') ? 'invalid' : '' }}"
                id="f-name" name="name" type="text"
                placeholder="Алексей" autocomplete="given-name"
                value="{{ old('name') }}">
              <span class="field-icon" id="icon-name"></span>
            </div>
            <span class="field-msg hint" id="msg-name"></span>
          </div>
          <div class="field">
            <label class="field-label" for="f-phone">Телефон <span>*</span></label>
            <div class="input-wrap">
              <input class="field-input has-icon imask {{ $errors->has('phone') ? 'invalid' : '' }}"
                id="f-phone" name="phone" type="tel"
                placeholder="+7 (___) ___-__-__" autocomplete="tel"
                value="{{ old('phone') }}">
              <span class="field-icon" id="icon-phone"></span>
            </div>
            <span class="field-msg hint" id="msg-phone"></span>
          </div>
        </div>

        <!-- Email -->
        <div class="field">
          <label class="field-label" for="f-email">Электронная почта <span>*</span></label>
          <div class="input-wrap">
            <input class="field-input has-icon {{ $errors->has('email') ? 'invalid' : '' }}"
              id="f-email" name="email" type="email"
              placeholder="alex@company.ru" autocomplete="email"
              value="{{ old('email') }}">
            <span class="field-icon" id="icon-email"></span>
          </div>
          <span class="field-msg hint" id="msg-email"></span>
        </div>

        <!-- Пароль -->
        <div class="field">
          <label class="field-label" for="f-pw">Пароль <span>*</span></label>
          <div class="input-wrap">
            <input class="field-input has-icon {{ $errors->has('password') ? 'invalid' : '' }}"
              id="f-pw" name="password" type="password"
              placeholder="Не менее 5 символов" autocomplete="new-password">
            <button type="button" class="pw-toggle" id="pw-toggle" tabindex="-1" aria-label="Показать пароль">
              <svg id="eye-icon" width="18" height="18" viewBox="0 0 18 18" fill="none">
                <path d="M1.5 9s2.5-5 7.5-5 7.5 5 7.5 5-2.5 5-7.5 5-7.5-5-7.5-5z" stroke="currentColor" stroke-width="1.4"/>
                <circle cx="9" cy="9" r="2" stroke="currentColor" stroke-width="1.4"/>
              </svg>
            </button>
          </div>
          <div class="pw-strength" id="pw-strength" style="display:none;">
            <div class="pw-bars">
              <div class="pw-bar" id="bar1"></div>
              <div class="pw-bar" id="bar2"></div>
              <div class="pw-bar" id="bar3"></div>
            </div>
            <span class="pw-label" id="pw-label">Введите пароль</span>
          </div>
          <span class="field-msg hint" id="msg-pw"></span>
        </div>

        <!-- Повторить пароль -->
        <div class="field">
          <label class="field-label" for="f-pw2">Повторите пароль <span>*</span></label>
          <div class="input-wrap">
            <input class="field-input has-icon"
              id="f-pw2" name="password_confirmation" type="password"
              placeholder="Повторите пароль" autocomplete="new-password">
            <button type="button" class="pw-toggle" id="pw2-toggle" tabindex="-1" aria-label="Показать пароль">
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                <path d="M1.5 9s2.5-5 7.5-5 7.5 5 7.5 5-2.5 5-7.5 5-7.5-5-7.5-5z" stroke="currentColor" stroke-width="1.4"/>
                <circle cx="9" cy="9" r="2" stroke="currentColor" stroke-width="1.4"/>
              </svg>
            </button>
          </div>
          <span class="field-msg hint" id="msg-pw2"></span>
        </div>

        <!-- Согласие -->
        <input type="hidden" name="consent" id="consent-val" value="{{ old('consent', '0') }}">
        <div class="check-row">
          <div class="check-box {{ $errors->has('consent') ? 'error-border' : '' }}"
               id="consent-box" role="checkbox" aria-checked="false" tabindex="0">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
              <path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <label class="check-label" id="consent-label">
            Я соглашаюсь с <a href="#">политикой обработки персональных данных</a>
            и <a href="#">пользовательским соглашением</a>
          </label>
        </div>
        <span class="field-msg err" id="msg-consent" style="margin-top:-10px;">
          @error('consent'){{ $message }}@enderror
        </span>

        <!-- Submit -->
        <button type="submit" class="submit-btn" id="submit-btn">
          Создать аккаунт
        </button>

      </form>
    </div>
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

  function setFieldState(input, iconEl, msgEl, state, msg) {
    input.classList.remove('valid','invalid');
    if (state === 'ok')  { input.classList.add('valid');   iconEl.innerHTML = svgOk; }
    if (state === 'err') { input.classList.add('invalid'); iconEl.innerHTML = svgErr; }
    if (state === '')    { iconEl.innerHTML = ''; }
    msgEl.className = 'field-msg ' + (state === 'ok' ? 'ok' : state === 'err' ? 'err' : 'hint');
    msgEl.textContent = msg || '';
  }

  // Name
  const fName = document.getElementById('f-name');
  const iName = document.getElementById('icon-name');
  const mName = document.getElementById('msg-name');
  fName.addEventListener('blur', validateName);
  fName.addEventListener('input', () => { if (fName.classList.contains('invalid')) validateName(); });
  function validateName() {
    const v = fName.value.trim();
    if (!v) return setFieldState(fName, iName, mName, 'err', 'Введите ваше имя');
    if (v.length < 2) return setFieldState(fName, iName, mName, 'err', 'Слишком короткое имя');
    setFieldState(fName, iName, mName, 'ok', '');
  }

  // Phone
  const fPhone = document.getElementById('f-phone');
  const iPhone = document.getElementById('icon-phone');
  const mPhone = document.getElementById('msg-phone');
  fPhone.addEventListener('blur', validatePhone);
  fPhone.addEventListener('input', () => { if (fPhone.classList.contains('invalid')) validatePhone(); });
  document.addEventListener('DOMContentLoaded', () => {
    fPhone._iMask?.on('accept', () => { if (fPhone.classList.contains('invalid')) validatePhone(); });
  });
  function validatePhone() {
    const unmasked = fPhone._iMask ? fPhone._iMask.unmaskedValue : fPhone.value.replace(/\D/g, '');
    if (unmasked.length < 11) return setFieldState(fPhone, iPhone, mPhone, 'err', 'Введите корректный номер');
    setFieldState(fPhone, iPhone, mPhone, 'ok', '');
  }

  // Email
  const fEmail = document.getElementById('f-email');
  const iEmail = document.getElementById('icon-email');
  const mEmail = document.getElementById('msg-email');
  fEmail.addEventListener('blur', validateEmail);
  fEmail.addEventListener('input', () => { if (fEmail.classList.contains('invalid')) validateEmail(); });
  function validateEmail() {
    const v = fEmail.value.trim();
    if (!v) return setFieldState(fEmail, iEmail, mEmail, 'err', 'Введите электронную почту');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v))
      return setFieldState(fEmail, iEmail, mEmail, 'err', 'Некорректный адрес почты');
    setFieldState(fEmail, iEmail, mEmail, 'ok', '');
  }

  // Password
  const fPw   = document.getElementById('f-pw');
  const mPw   = document.getElementById('msg-pw');
  const pwStr = document.getElementById('pw-strength');
  const bars  = [document.getElementById('bar1'), document.getElementById('bar2'), document.getElementById('bar3')];
  const pwLbl = document.getElementById('pw-label');

  function getStrength(pw) {
    if (pw.length >= 8) return 3;
    if (pw.length >= 5) return 2;
    return 1;
  }

  fPw.addEventListener('input', updateStrength);
  fPw.addEventListener('blur', validatePw);

  function updateStrength() {
    const v = fPw.value;
    if (!v) { pwStr.style.display='none'; return; }
    pwStr.style.display='flex';
    const s = getStrength(v);
    bars.forEach((b,i) => {
      b.className = 'pw-bar';
      if (i < s) b.classList.add(s===1?'weak':s===2?'medium':'strong');
    });
    pwLbl.className = 'pw-label ' + (s===1?'weak':s===2?'medium':'strong');
    pwLbl.textContent = s===1?'Слабый пароль':s===2?'Средний пароль':'Надёжный пароль';
    if (fPw.classList.contains('invalid')) validatePw();
    if (fPw2.value) validatePw2();
  }

  function validatePw() {
    const v = fPw.value;
    if (!v) return setFieldState(fPw, {innerHTML:''}, mPw, 'err', 'Введите пароль');
    if (v.length < 5) return setFieldState(fPw, {innerHTML:''}, mPw, 'err', 'Пароль должен быть не менее 5 символов');
    setFieldState(fPw, {innerHTML:''}, mPw, 'ok', '');
    if (fPw2.value) validatePw2();
  }

  // Password toggle
  function makeToggle(btn, input) {
    btn.addEventListener('click', () => {
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.style.color = show ? 'var(--accent)' : '';
    });
  }
  makeToggle(document.getElementById('pw-toggle'), fPw);
  makeToggle(document.getElementById('pw2-toggle'), document.getElementById('f-pw2'));

  // Confirm password
  const fPw2 = document.getElementById('f-pw2');
  const mPw2 = document.getElementById('msg-pw2');
  fPw2.addEventListener('blur', validatePw2);
  fPw2.addEventListener('input', () => { if (fPw2.classList.contains('invalid')) validatePw2(); });
  function validatePw2() {
    const v = fPw2.value;
    if (!v) return setFieldState(fPw2, {innerHTML:''}, mPw2, 'err', 'Повторите пароль');
    if (v !== fPw.value) return setFieldState(fPw2, {innerHTML:''}, mPw2, 'err', 'Пароли не совпадают');
    setFieldState(fPw2, {innerHTML:''}, mPw2, 'ok', 'Пароли совпадают');
  }

  // Consent checkbox
  const consentBox = document.getElementById('consent-box');
  const consentLbl = document.getElementById('consent-label');
  const consentVal = document.getElementById('consent-val');
  const mConsent   = document.getElementById('msg-consent');
  let   consented  = false;

  function toggleConsent() {
    consented = !consented;
    consentBox.classList.toggle('checked', consented);
    consentBox.setAttribute('aria-checked', consented);
    consentBox.classList.remove('error-border');
    consentVal.value = consented ? '1' : '0';
    if (consented) mConsent.textContent = '';
  }
  consentBox.addEventListener('click', toggleConsent);
  consentLbl.addEventListener('click', toggleConsent);
  consentBox.addEventListener('keydown', e => {
    if (e.key === ' ' || e.key === 'Enter') { e.preventDefault(); toggleConsent(); }
  });

  // Client-side submit guard
  document.getElementById('reg-form').addEventListener('submit', function(e) {
    validateName(); validatePhone(); validateEmail(); validatePw(); validatePw2();
    let ok = true;
    if (!fName.classList.contains('valid'))  ok = false;
    if (!fPhone.classList.contains('valid')) ok = false;
    if (!fEmail.classList.contains('valid')) ok = false;
    if (fPw.value.length < 5)               ok = false;
    if (fPw.value !== fPw2.value)           ok = false;
    if (!consented) {
      consentBox.classList.add('error-border');
      mConsent.textContent = 'Необходимо согласие на обработку персональных данных';
      ok = false;
    }
    if (!ok) { e.preventDefault(); return; }
    document.getElementById('submit-btn').disabled = true;
    document.getElementById('submit-btn').textContent = 'Создаём аккаунт…';
  });
</script>
@endpush
