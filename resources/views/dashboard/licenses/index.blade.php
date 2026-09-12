@extends('dashboard.layout')
@section('title', 'Licenses')
@section('content')
    <h1>Licenses</h1>
    <table>
        <thead><tr><th>ID</th><th>Customer</th><th>Plan</th><th>Status</th><th>Domain</th><th>Provisioning</th><th>Custom domain</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach ($licenses as $license)
            @php($instance = $license->instance)
            <tr>
                <td>{{ $license->id }}</td>
                <td>{{ $license->customer->name }}</td>
                <td>
                    <form class="inline" method="POST" action="{{ route('dashboard.licenses.change-plan', $license) }}">
                        @csrf
                        <select name="plan_id" onchange="this.form.submit()">
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}" @selected($license->plan_id === $plan->id)>{{ $plan->name }}</option>
                            @endforeach
                        </select>
                    </form>
                </td>
                <td class="status status-{{ $license->status }}">{{ $license->status }}</td>
                <td>{{ $instance?->default_domain ?? '—' }}</td>
                <td>
                    {{ $instance?->provisioning_status ?? '—' }}
                    @if ($instance?->provisioning_error)
                        <div style="color:#b3261e;font-size:12px">{{ $instance->provisioning_error }}</div>
                    @endif
                </td>
                <td>
                    @if ($instance?->custom_domain)
                        {{ $instance->custom_domain }}
                        <div style="font-size:12px;color:#555">CNAME → {{ $instance->custom_domain_dns_target }}</div>
                    @else
                        <form class="inline" method="POST" action="{{ route('dashboard.licenses.add-custom-domain', $license) }}">
                            @csrf
                            <input type="text" name="domain" placeholder="customer-owned-domain.com" style="width:160px">
                            <button type="submit">Add to Railway</button>
                        </form>
                    @endif
                </td>
                <td>
                    @if ($license->status !== 'suspended')
                        <form class="inline" method="POST" action="{{ route('dashboard.licenses.suspend', $license) }}">
                            @csrf<button type="submit">Suspend</button>
                        </form>
                    @else
                        <form class="inline" method="POST" action="{{ route('dashboard.licenses.reinstate', $license) }}">
                            @csrf<button type="submit">Reinstate</button>
                        </form>
                    @endif
                    <form class="inline" method="POST" action="{{ route('dashboard.licenses.reissue-secret', $license) }}" onsubmit="return confirm('Reissue secret? The instance will fail phone-home until its .env is updated.')">
                        @csrf<button type="submit">Reissue secret</button>
                    </form>
                    <form class="inline" method="POST" action="{{ route('dashboard.licenses.extend-grace', $license) }}">
                        @csrf<button type="submit">+14d grace</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $licenses->links() }}
@endsection
