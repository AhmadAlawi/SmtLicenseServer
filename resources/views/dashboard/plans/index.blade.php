@extends('dashboard.layout')
@section('title', 'Plans')
@section('content')
    <h1>Plans</h1>
    <table>
        <thead><tr><th>Code</th><th>Name</th><th>Seat limit</th><th>Features</th><th>Stripe price</th><th>Active</th></tr></thead>
        <tbody>
        @foreach ($plans as $plan)
            <tr>
                <td>{{ $plan->code }}</td>
                <td>{{ $plan->name }}</td>
                <td>{{ $plan->seat_limit ?? 'Unlimited' }}</td>
                <td>{{ collect($plan->features ?? [])->filter()->keys()->implode(', ') ?: '—' }}</td>
                <td>{{ $plan->stripe_price_id ?? '—' }}</td>
                <td>{{ $plan->is_active ? 'Yes' : 'No' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
