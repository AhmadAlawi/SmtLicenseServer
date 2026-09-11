@extends('dashboard.layout')
@section('title', 'Signups')
@section('content')
    <h1>Signups</h1>
    <table>
        <thead><tr><th>Company</th><th>Email</th><th>Plan</th><th>Source</th><th>Medium</th><th>Campaign</th><th>Converted</th><th>Created</th></tr></thead>
        <tbody>
        @foreach ($signups as $signup)
            <tr>
                <td>{{ $signup->company_name }}</td>
                <td>{{ $signup->admin_email }}</td>
                <td>{{ $signup->plan_code }}</td>
                <td>{{ $signup->utm_source ?? '—' }}</td>
                <td>{{ $signup->utm_medium ?? '—' }}</td>
                <td>{{ $signup->utm_campaign ?? '—' }}</td>
                <td>{{ isset($convertedEmails[$signup->admin_email]) ? 'Yes' : 'No' }}</td>
                <td>{{ $signup->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $signups->links() }}
@endsection
