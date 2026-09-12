@extends('emails.layout')
@section('subject', 'Update available: version ' . $release->version)
@section('content')
<p style="margin:0 0 16px 0;">Hi {{ $customer->name }},</p>
<p style="margin:0 0 20px 0;">A new version is available: <strong style="color:#3c6a00;">{{ $release->version }}</strong></p>
@if ($release->changelog)
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px 0;">
<tr><td style="background-color:#f2f5ef;border-radius:8px;padding:16px;font-size:14px;line-height:22px;white-space:pre-line;">{{ $release->changelog }}</td></tr>
</table>
@endif
<p style="margin:0 0 12px 0;">Nothing happens automatically &mdash; install it whenever suits you, from Settings &rarr; Updates in your own panel:</p>
@forelse ($domains as $domain)
<p style="margin:0 0 8px 0;"><a href="https://{{ $domain }}/admin/settings/updates" style="color:#3c6a00;">https://{{ $domain }}/admin/settings/updates</a></p>
@empty
<p style="margin:0;">Settings &rarr; Updates in your admin panel.</p>
@endforelse
@endsection
