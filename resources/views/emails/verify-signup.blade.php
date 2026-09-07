<!DOCTYPE html>
<html>
<body style="font-family: system-ui, sans-serif; color: #1a1a1a;">
<p>Hi {{ $signup->admin_name }},</p>
<p>Thanks for starting your subscription to <strong>{{ $signup->company_name }}</strong>'s POS instance
   at <strong>{{ $signup->subdomain_slug }}.{{ config('services.platform.root_domain') ?: 'yourdomain.com' }}</strong>.</p>
<p>Confirm your email to continue to payment:</p>
<p><a href="{{ $verifyUrl }}" style="display:inline-block;background:#1a1a2e;color:#fff;padding:10px 20px;border-radius:4px;text-decoration:none">Confirm &amp; continue to payment</a></p>
<p style="font-size:12px;color:#666">This link expires in 24 hours. If you didn't request this, ignore this email.</p>
</body>
</html>
