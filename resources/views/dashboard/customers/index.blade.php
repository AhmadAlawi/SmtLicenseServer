@extends('dashboard.layout')
@section('title', 'Customers')
@section('content')
    <h1>Customers</h1>
    <table>
        <thead><tr><th>Name</th><th>Email</th><th>Licenses</th><th>Stripe</th></tr></thead>
        <tbody>
        @foreach ($customers as $customer)
            <tr>
                <td><a href="{{ route('dashboard.customers.show', $customer) }}">{{ $customer->name }}</a></td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->licenses_count }}</td>
                <td>{{ $customer->stripe_id ?? '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $customers->links() }}
@endsection
