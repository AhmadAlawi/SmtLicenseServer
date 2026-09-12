@extends('dashboard.layout')
@section('title', 'Promo Codes')
@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <h1 style="margin:0">Promo Codes</h1>
        <a href="{{ route('dashboard.promo-codes.create') }}" class="btn">New promo code</a>
    </div>
    @if (session('status'))
        <p style="color:#2e7d32">{{ session('status') }}</p>
    @endif
    <table>
        <thead><tr><th>Code</th><th>Discount</th><th>Duration</th><th>Redemptions</th><th>Expires</th><th>Active</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach ($promoCodes as $promo)
            <tr>
                <td>{{ $promo->code }}</td>
                <td>{{ $promo->discount_type === 'percent' ? "{$promo->discount_value}%" : '$'.number_format($promo->discount_value / 100, 2) }} off</td>
                <td>
                    @if ($promo->duration === 'once') First charge only
                    @elseif ($promo->duration === 'forever') Every renewal, forever
                    @else {{ $promo->duration_in_months }} month{{ $promo->duration_in_months > 1 ? 's' : '' }}, then full price
                    @endif
                </td>
                <td>{{ $promo->max_redemptions ?? 'Unlimited' }}</td>
                <td>{{ $promo->expires_at?->format('Y-m-d') ?? '—' }}</td>
                <td>{{ $promo->is_active ? 'Yes' : 'No' }}</td>
                <td>
                    @if ($promo->is_active)
                        <form class="inline" method="POST" action="{{ route('dashboard.promo-codes.deactivate', $promo) }}" onsubmit="return confirm('Deactivate {{ $promo->code }}? Existing subscriptions using it keep their current discount, but nobody can redeem it again.')">
                            @csrf<button type="submit">Deactivate</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $promoCodes->links() }}
@endsection
