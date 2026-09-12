@extends('emails.layout')
@section('subject', 'Your POS instance is ready')
@section('content')
<p style="margin:0 0 16px 0;">Good news &mdash; your POS instance is ready.</p>
<table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 24px 0;"><tr><td style="border-radius:999px;background-color:#3c6a00;">
<a href="https://{{ $instance->default_domain }}" style="display:inline-block;padding:12px 28px;font-weight:600;font-size:14px;color:#ffffff;text-decoration:none;">Open your POS</a>
</td></tr></table>
<p style="margin:0 0 8px 0;">Log in with the admin email and password you chose at signup:</p>
<p style="margin:0 0 24px 0;font-weight:600;">{{ $adminEmail }}</p>
<p style="margin:0;font-size:13px;color:#707b62;">If you've forgotten your password, use the "Forgot password?" link on that login page.</p>
@endsection
