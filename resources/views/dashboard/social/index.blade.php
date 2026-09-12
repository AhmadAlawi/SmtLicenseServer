@extends('dashboard.layout')
@section('title', 'Social')
@section('content')
    <h1>Social (Ads)</h1>
    <p class="hint">Ads-only connection — used to create/manage Facebook &amp; Instagram ad campaigns. No organic posting permission is requested.</p>

    @if ($connection)
        <table style="max-width:480px">
            <tbody>
                <tr><th>Ad account</th><td>{{ $connection->ad_account_name ?? $connection->ad_account_id }}</td></tr>
                <tr><th>Page</th><td>{{ $connection->page_name ?? '—' }}</td></tr>
                <tr><th>Instagram account</th><td>{{ $connection->instagram_business_account_id ?? '—' }}</td></tr>
                <tr><th>Token expires</th><td>{{ $connection->token_expires_at?->format('Y-m-d') ?? '—' }}</td></tr>
            </tbody>
        </table>
        <form method="POST" action="{{ route('dashboard.social.disconnect') }}" style="margin-top:16px" onsubmit="return confirm('Disconnect Facebook?');">
            @csrf
            <button type="submit">Disconnect</button>
        </form>
    @else
        <a href="{{ route('dashboard.social.connect') }}" class="btn">Connect with Facebook</a>
    @endif
@endsection
