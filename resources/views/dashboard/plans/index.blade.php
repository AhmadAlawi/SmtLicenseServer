@extends('dashboard.layout')
@section('title', 'Plans')
@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <h1 style="margin:0">Plans</h1>
        <a href="{{ route('dashboard.plans.create') }}" class="btn">New plan</a>
    </div>
    <table>
        <thead><tr><th>Code</th><th>Name</th><th>Display price</th><th>Seat limit</th><th>Features</th><th>Stripe price</th><th>Active</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach ($plans as $plan)
            <tr>
                <td>{{ $plan->code }}</td>
                <td>{{ $plan->name }}</td>
                <td>{{ $plan->display_price ?? '—' }}</td>
                <td>{{ $plan->seat_limit ?? 'Unlimited' }}</td>
                <td>{{ collect($plan->features ?? [])->filter()->keys()->implode(', ') ?: '—' }}</td>
                <td>{{ $plan->stripe_price_id ?? '—' }}</td>
                <td>{{ $plan->is_active ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('dashboard.plans.edit', $plan) }}">Edit</a>
                    <form class="inline" method="POST" action="{{ route('dashboard.plans.toggle-active', $plan) }}" style="display:inline">
                        @csrf
                        <button type="submit">{{ $plan->is_active ? 'Deactivate' : 'Activate' }}</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
