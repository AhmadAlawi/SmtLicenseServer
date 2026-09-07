<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reset password — SMT License Server</title>
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
<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <h2>Set a new password</h2>
    @error('email')<div class="error">{{ $message }}</div>@enderror
    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required autofocus>
    <label for="password">New password</label>
    <input id="password" name="password" type="password" minlength="8" required>
    <label for="password_confirmation">Confirm password</label>
    <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required>
    <button type="submit">Reset password</button>
</form>
</body>
</html>
