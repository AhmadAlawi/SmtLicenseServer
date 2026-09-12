@extends('emails.layout')
@section('subject', 'Confirm your email to continue')
@section('content')
<p style="margin:0 0 16px 0;">Hi {{ $signup->admin_name }},</p>
<p style="margin:0 0 16px 0;">Thanks for starting your subscription. Your new POS instance for <strong>{{ $signup->company_name }}</strong> is being set up at:</p>
<p style="margin:0 0 24px 0;font-weight:600;color:#3c6a00;">{{ $signup->subdomain_slug }}.{{ config('services.platform.root_domain') ?: 'yourdomain.com' }}</p>
<p style="margin:0 0 24px 0;">Confirm your email to continue to payment:</p>
<table role="presentation" cellpadding="0" cellspacing="0"><tr><td style="border-radius:999px;background-color:#3c6a00;">
<a href="{{ $verifyUrl }}" style="display:inline-block;padding:12px 28px;font-weight:600;font-size:14px;color:#ffffff;text-decoration:none;">Confirm &amp; continue to payment</a>
</td></tr></table>
<p style="margin:24px 0 0 0;font-size:12px;color:#707b62;">This link expires in 24 hours. If you didn't request this, you can safely ignore this email.</p>
@endsection
