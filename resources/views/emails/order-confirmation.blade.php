@extends('emails.layout')
@section('subject', 'Your subscription is confirmed')
@section('content')
<p style="margin:0 0 16px 0;">Hi {{ $customer->name }},</p>
<p style="margin:0 0 16px 0;">Thanks &mdash; your subscription to the <strong>{{ $plan->name }}</strong> plan is confirmed.</p>
<p style="margin:0 0 8px 0;">Your instance is being set up now at:</p>
<p style="margin:0 0 24px 0;font-weight:600;color:#3c6a00;">{{ $subdomainSlug }}.{{ $rootDomain ?: 'yourdomain.com' }}</p>
<p style="margin:0;">You'll get another email the moment it's ready to log into &mdash; usually within a few minutes.</p>
@endsection
