<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Forgot password — Tillora</title>
    <style>
        body { font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #1a1a2e; }
        form { background: #fff; padding: 32px; border-radius: 8px; width: 320px; }
        label { display: block; margin-bottom: 4px; font-size: 13px; }
        input { width: 100%; padding: 8px; margin-bottom: 16px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #1a1a2e; color: #fff; border: none; border-radius: 4px; }
        .error { color: #b3261e; font-size: 13px; margin-bottom: 12px; }
        .status { color: #0a7a2f; font-size: 13px; margin-bottom: 12px; }
    </style>
</head>
<body>
<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <h2>Reset password</h2>
    <p style="font-size:13px;color:#555">Enter your email and we'll send a reset link.</p>
    @if (session('status'))<div class="status">{{ session('status') }}</div>@endif
    @error('email')<div class="error">{{ $message }}</div>@enderror
    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
    <button type="submit">Send reset link</button>
</form>
</body>
</html>
