<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <title>@yield('title') — СчётОк</title>
    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="@yield('keywords')">
    <meta name="robots" content="noindex, nofollow">
</head>
<body class="auth-body">

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
    <div class="nav-hint">
      @yield('nav-hint')
    </div>
  </div>
</nav>

<x-message.message/>
<x-message.message_error/>

<main class="page-body">
  @yield('content')
</main>

@stack('scripts')
</body>
</html>
