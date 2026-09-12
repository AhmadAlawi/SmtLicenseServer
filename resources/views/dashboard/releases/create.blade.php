@extends('dashboard.layout')
@section('title', 'Publish release')
@section('content')
    <h1>Publish release</h1>
    @if ($errors->any())
        <div class="flash" style="background:#fbe1e1">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('dashboard.releases.store') }}" enctype="multipart/form-data" style="max-width:480px">
        @csrf

        <label>Version</label>
        <input type="text" name="version" value="{{ old('version') }}" placeholder="1.0.5" required>
        <div class="hint">Must be greater than every tenant's current version for the update to be offered — SaasPOS compares these as semver.</div>

        <label>Channel</label>
        <select name="channel" required>
            <option value="stable" {{ old('channel') === 'stable' ? 'selected' : '' }}>Stable</option>
            <option value="beta" {{ old('channel') === 'beta' ? 'selected' : '' }}>Beta</option>
        </select>

        <label>Changelog (shown to customers in the update email and panel)</label>
        <textarea name="changelog" rows="6">{{ old('changelog') }}</textarea>

        <label>Release zip</label>
        <input type="file" name="zip" accept=".zip" required>
        <div class="hint">Signed automatically on publish with this server's own key — no manual signing step. Publishing immediately emails every active customer.</div>

        <button type="submit" class="btn" style="margin-top:16px">Publish and notify customers</button>
    </form>
@endsection
