<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Dashboard') — SMT License Server</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f7f7f8; color: #1a1a1a; }
        nav { background: #1a1a2e; padding: 12px 24px; display: flex; gap: 20px; align-items: center; }
        nav a { color: #cfd2ff; text-decoration: none; font-size: 14px; }
        nav a.brand { color: #fff; font-weight: 600; margin-right: 20px; }
        main { padding: 24px; max-width: 1100px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { text-align: left; padding: 8px 12px; border-bottom: 1px solid #e5e5e5; font-size: 14px; }
        th { background: #eee; }
        .status { font-weight: 600; }
        .status-valid { color: #0a7a2f; }
        .status-suspended, .status-invalid { color: #b3261e; }
        .status-past_due { color: #b36b00; }
        form.inline { display: inline; }
        button { cursor: pointer; }
        .flash { background: #dff0d8; padding: 10px 16px; margin-bottom: 16px; border-radius: 4px; }
    </style>
</head>
<body>
<nav>
    <a class="brand" href="{{ route('dashboard.home') }}">SMT License Server</a>
    <a href="{{ route('dashboard.customers.index') }}">Customers</a>
    <a href="{{ route('dashboard.licenses.index') }}">Licenses</a>
    <a href="{{ route('dashboard.plans.index') }}">Plans</a>
    <form class="inline" method="POST" action="{{ route('dashboard.logout') }}" style="margin-left:auto">
        @csrf
        <button type="submit">Log out</button>
    </form>
</nav>
<main>
    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif
    @yield('content')
</main>
</body>
</html>
