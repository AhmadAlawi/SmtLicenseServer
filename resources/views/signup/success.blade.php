<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>You're subscribed — Tillora</title>
    <style>
        body { font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #f7f7f8; text-align: center; }
        div { max-width: 480px; }
    </style>
@include('partials.analytics')
</head>
<body>
<div>
    <div style="margin-bottom:16px">@include('partials.logo', ['size' => 24])</div>
    <h1>Thanks — you're subscribed.</h1>
    <p>Your instance is being provisioned now. You'll receive an email with your login details and instance URL shortly.</p>
</div>
@if (config('services.google_analytics.id'))
<script>
    if (typeof gtag === 'function') {
        gtag('event', 'signup_completed');
    }
</script>
@endif
</body>
</html>
