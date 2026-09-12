<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — Tillora</title>
    <style>
        :root { --ink: #1a1a2e; --teal: #0d9488; --border: #e5e5e5; }
        * { box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; margin: 0; background: #f7f7f8; color: var(--ink); }
        nav { background: var(--ink); padding: 12px 24px; display: flex; gap: 20px; align-items: center; }
        nav a { color: #cfd2ff; text-decoration: none; font-size: 14px; }
        nav a.brand { margin-right: 12px; }
        nav a:hover { color: #fff; }
        main { padding: 24px; max-width: 1100px; margin: 0 auto; }
        h1 { font-size: 22px; }

        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; }
        th, td { text-align: left; padding: 10px 12px; border-bottom: 1px solid var(--border); font-size: 14px; }
        th { background: #f0f0f2; font-weight: 600; }

        .status { font-weight: 600; }
        .status-valid { color: #0a7a2f; }
        .status-suspended, .status-invalid { color: #b3261e; }
        .status-past_due { color: #b36b00; }

        form.inline { display: inline; }
        button { cursor: pointer; font-family: inherit; }
        .flash { background: #dff0d8; padding: 10px 16px; margin-bottom: 16px; border-radius: 6px; }

        .btn { display: inline-block; background: var(--ink); color: #fff; padding: 9px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600; border: none; cursor: pointer; }
        .btn:hover { opacity: 0.9; }

        label { display: block; font-size: 13px; color: #444; margin: 14px 0 4px; font-weight: 600; }
        label:first-of-type { margin-top: 0; }
        input[type=text], input[type=email], input[type=password], input[type=number] {
            width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: 6px; font-size: 14px; box-sizing: border-box;
        }
        .hint { font-size: 12px; color: #888; margin-top: 4px; }
    </style>
</head>
<body>
<nav>
    <a class="brand" href="{{ route('dashboard.home') }}">@include('partials.logo', ['size' => 22, 'dark' => true])</a>
    <a href="{{ route('dashboard.customers.index') }}">Customers</a>
    <a href="{{ route('dashboard.signups.index') }}">Signups</a>
    <a href="{{ route('dashboard.licenses.index') }}">Licenses</a>
    <a href="{{ route('dashboard.plans.index') }}">Plans</a>
    <a href="{{ route('dashboard.blog-posts.index') }}">Blog Posts</a>
    <a href="{{ route('dashboard.social.index') }}">Social</a>
    <a href="{{ route('dashboard.releases.index') }}">Releases</a>
    <a href="{{ route('dashboard.promo-codes.index') }}">Promo Codes</a>
    <form class="inline" method="POST" action="{{ route('dashboard.logout') }}" style="margin-left:auto">
        @csrf
        <button type="submit" style="background:transparent;border:1px solid #444;color:#cfd2ff;padding:6px 12px;border-radius:6px">Log out</button>
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
