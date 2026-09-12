<!DOCTYPE html>
<html>
<body style="font-family: system-ui, sans-serif; color: #1a1a1a;">
<p>Hi {{ $customer->name }},</p>
<p>A new version is available: <strong>{{ $release->version }}</strong>.</p>
@if ($release->changelog)
<p style="white-space: pre-line;">{{ $release->changelog }}</p>
@endif
<p>Nothing happens automatically — install it whenever suits you, from Settings → Updates in your own panel:</p>
<ul>
@forelse ($domains as $domain)
    <li><a href="https://{{ $domain }}/admin/settings/updates">https://{{ $domain }}/admin/settings/updates</a></li>
@empty
    <li>Settings → Updates in your admin panel</li>
@endforelse
</ul>
</body>
</html>
