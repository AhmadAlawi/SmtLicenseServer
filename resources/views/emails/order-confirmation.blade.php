<!DOCTYPE html>
<html>
<body style="font-family: system-ui, sans-serif; color: #1a1a1a;">
<p>Hi {{ $customer->name }},</p>
<p>Thanks — your subscription to the <strong>{{ $plan->name }}</strong> plan is confirmed.</p>
<p>Your instance is being set up now at <strong>{{ $subdomainSlug }}.{{ $rootDomain ?: 'yourdomain.com' }}</strong>.
   You'll get another email the moment it's ready to log into — usually within a few minutes.</p>
</body>
</html>
