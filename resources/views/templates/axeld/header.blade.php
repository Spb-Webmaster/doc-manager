<nav class="nav">
  <div class="wrap nav-inner">
    <a href="{{ route('home') }}" class="logo">
      <div class="logo-icon">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
          <rect x="2.5" y="1.5" width="11" height="14" rx="1.8" stroke="#fff" stroke-width="1.5"/>
          <path d="M11 1.5v4H15" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M5.5 8h7M5.5 11h7M5.5 14h4" stroke="#fff" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
      </div>
      СчётОк
    </a>
    <div class="nav-btns">
      @guest
        <a href="{{ route('login') }}" class="btn btn-ghost">Войти</a>
        <a href="{{ route('register') }}" class="btn btn-primary">Зарегистрироваться</a>
      @else
        <a href="{{ route('cabinet') }}" class="nav-user">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          Кабинет
        </a>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-ghost">Выйти</button>
        </form>
      @endguest
    </div>
  </div>
</nav>
