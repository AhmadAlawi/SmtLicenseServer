@extends('dashboard.layout')
@section('title', 'Releases')
@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <h1 style="margin:0">Releases</h1>
        <a href="{{ route('dashboard.releases.create') }}" class="btn">Publish release</a>
    </div>
    @if (session('status'))
        <p style="color:#2e7d32">{{ session('status') }}</p>
    @endif
    <table>
        <thead><tr><th>Version</th><th>Channel</th><th>Published</th><th>By</th><th>Changelog</th></tr></thead>
        <tbody>
        @foreach ($releases as $release)
            <tr>
                <td>{{ $release->version }}</td>
                <td>{{ $release->channel }}</td>
                <td>{{ $release->published_at?->format('Y-m-d H:i') ?? '—' }}</td>
                <td>{{ $release->publisher?->name ?? '—' }}</td>
                <td>{{ \Illuminate\Support\Str::limit($release->changelog, 80) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $releases->links() }}
@endsection
