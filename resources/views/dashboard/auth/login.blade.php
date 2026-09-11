<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Staff login — Tillora</title>
    <style>
        body { font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #1a1a2e; }
        form { background: #fff; padding: 32px; border-radius: 8px; width: 320px; }
        label { display: block; margin-bottom: 4px; font-size: 13px; }
        input { width: 100%; padding: 8px; margin-bottom: 16px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #1a1a2e; color: #fff; border: none; border-radius: 4px; }
        .error { color: #b3261e; font-size: 13px; margin-bottom: 12px; }
    </style>
</head>
<body>
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div style="margin-bottom:12px">@include('partials.logo', ['size' => 22])</div>
    <h2 style="font-size:16px;color:#666;margin:0 0 16px">Staff login</h2>
    @error('email')<div class="error">{{ $message }}</div>@enderror
    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
    <label for="password">Password</label>
    <input id="password" name="password" type="password" required>
    <button type="submit">Log in</button>
    <p style="text-align:center;margin-top:12px"><a href="{{ route('password.request') }}">Forgot your password?</a></p>
</form>
</body>
</html>
