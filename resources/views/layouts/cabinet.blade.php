<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
  <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
  <title>@yield('title', 'Кабинет — СчётОк')</title>
</head>
<body class="cab-body">

<x-message.message/>
<x-message.message_error/>

<div class="shell">

  <!-- ══ SIDEBAR ══ -->
  <aside class="sidebar">
    <a href="{{ route('home') }}" class="sb-logo">
      <div class="sb-logo-icon">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
          <rect x="2" y="1" width="10" height="13" rx="1.8" stroke="#fff" stroke-width="1.4"/>
          <path d="M9.5 1v3.5H13" stroke="#fff" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M4.5 7h7M4.5 9.5h7M4.5 12h4" stroke="#fff" stroke-width="1.3" stroke-linecap="round"/>
        </svg>
      </div>
      СчётОк
    </a>

    <nav class="sb-nav">
      <a class="nav-item {{ request()->routeIs('cabinet') ? 'active' : '' }}" href="{{ route('cabinet') }}">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1.5"/>
          <rect x="9" y="1.5" width="5.5" height="5.5" rx="1.5"/>
          <rect x="1.5" y="9" width="5.5" height="5.5" rx="1.5"/>
          <rect x="9" y="9" width="5.5" height="5.5" rx="1.5"/>
        </svg>
        Кабинет
      </a>
      <a class="nav-item {{ request()->routeIs('cabinet.invoices', 'cabinet.invoices.create') ? 'active' : '' }}" href="{{ route('cabinet.invoices') }}">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 2H4a1.5 1.5 0 0 0-1.5 1.5v9A1.5 1.5 0 0 0 4 14h8a1.5 1.5 0 0 0 1.5-1.5V7z"/>
          <path d="M9 2v5h5M5 7.5h6M5 10h6M5 12.5h3.5"/>
        </svg>
        Счета
      </a>
      <a class="nav-item {{ request()->routeIs('cabinet.acts', 'cabinet.acts.create') ? 'active' : '' }}" href="{{ route('cabinet.acts') }}">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 2H4a1.5 1.5 0 0 0-1.5 1.5v9A1.5 1.5 0 0 0 4 14h8a1.5 1.5 0 0 0 1.5-1.5V7z"/>
          <path d="M9 2v5h5M5.5 10.5l2 2 3.5-3.5"/>
        </svg>
        Акты
      </a>
      <a class="nav-item {{ request()->routeIs('cabinet.templates') ? 'active' : '' }}" href="{{ route('cabinet.templates') }}">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 2H4a1.5 1.5 0 0 0-1.5 1.5v9A1.5 1.5 0 0 0 4 14h8a1.5 1.5 0 0 0 1.5-1.5V7z"/>
          <path d="M9 2v5h5"/>
          <circle cx="8" cy="10" r="2"/>
          <path d="M8 8v0M8 8.5v3"/>
        </svg>
        Шаблоны
      </a>
      <a class="nav-item {{ request()->routeIs('cabinet.contractors') ? 'active' : '' }}" href="{{ route('cabinet.contractors') }}">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="5.5" cy="5.5" r="2.5"/>
          <circle cx="11" cy="5.5" r="2.5"/>
          <path d="M1 13c0-2.5 2-4.5 4.5-4.5h4c2.5 0 4.5 2 4.5 4.5"/>
        </svg>
        Контрагенты
      </a>
      <div class="sb-sep"></div>
      <a class="nav-item {{ request()->routeIs('cabinet.settings') ? 'active' : '' }}" href="{{ route('cabinet.settings') }}">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
          <circle cx="8" cy="8" r="2.5"/>
          <path d="M8 1.5v2M8 12.5v2M1.5 8h2M12.5 8h2M3.4 3.4l1.4 1.4M11.2 11.2l1.4 1.4M3.4 12.6l1.4-1.4M11.2 4.8l1.4-1.4"/>
        </svg>
        Настройки
      </a>
    </nav>

    <div class="sb-user">
      <div class="sb-user-row">
        <div class="sb-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
        <div>
          <div class="sb-uname">{{ auth()->user()->name }}</div>
          <div class="sb-urole">{{ auth()->user()->email }}</div>
        </div>
      </div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="sb-logout">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12.5H3a1.5 1.5 0 0 1-1.5-1.5V3A1.5 1.5 0 0 1 3 1.5h2M9.5 10l3-3-3-3M12.5 7H5.5"/>
          </svg>
          Выйти
        </button>
      </form>
    </div>
  </aside>

  <!-- ══ MAIN ══ -->
  <div class="main">
    @yield('content')
  </div>

</div>

<script src="{{ asset('js/drag-sort.js') }}"></script>
@stack('scripts')
</body>
</html>
