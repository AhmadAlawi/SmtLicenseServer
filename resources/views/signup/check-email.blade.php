<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Check your email — Tillora</title>
    <style>
        body { font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #f7f7f8; text-align: center; }
        div { max-width: 480px; }
    </style>
@include('partials.analytics')
</head>
<body>
<div>
    <div style="margin-bottom:16px">@include('partials.logo', ['size' => 24])</div>
    <h1>Check your email</h1>
    <p>We sent a confirmation link to <strong>{{ $email }}</strong>. Click it to continue to payment.</p>
    <p style="font-size:13px;color:#666">The link expires in 24 hours.</p>
</div>
</body>
</html>
