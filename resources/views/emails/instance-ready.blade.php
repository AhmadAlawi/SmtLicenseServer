<!DOCTYPE html>
<html>
<body style="font-family: system-ui, sans-serif; color: #1a1a1a;">
<p>Good news — your POS instance is ready.</p>
<p><a href="https://{{ $instance->default_domain }}" style="display:inline-block;background:#1a1a2e;color:#fff;padding:10px 20px;border-radius:4px;text-decoration:none">https://{{ $instance->default_domain }}</a></p>
<p>Log in with the admin email and password you chose at signup: <strong>{{ $adminEmail }}</strong>.</p>
<p>If you've forgotten your password, use the "Forgot password?" link on that login page.</p>
</body>
</html>
