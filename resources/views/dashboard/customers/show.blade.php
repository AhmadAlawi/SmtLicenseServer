@extends('dashboard.layout')
@section('title', $customer->name)
@section('content')
    <h1>{{ $customer->name }}</h1>
    <p>{{ $customer->email }} — Stripe: {{ $customer->stripe_id ?? '—' }}</p>

    <h2>Licenses</h2>
    <table>
        <thead><tr><th>ID</th><th>Plan</th><th>Status</th><th>Domain</th><th>Last seen</th></tr></thead>
        <tbody>
        @foreach ($customer->licenses as $license)
            <tr>
                <td>{{ $license->id }}</td>
                <td>{{ $license->plan?->name ?? '—' }}</td>
                <td class="status status-{{ $license->status }}">{{ $license->status }}</td>
                <td>{{ $license->instance_domain ?? '—' }}</td>
                <td>{{ $license->last_seen_at?->diffForHumans() ?? 'never' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
