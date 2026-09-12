@extends('dashboard.layout')
@section('title', 'New promo code')
@section('content')
    <h1>New promo code</h1>
    @if ($errors->any())
        <div class="flash" style="background:#fbe1e1">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('dashboard.promo-codes.store') }}" style="max-width:480px">
        @csrf

        <label>Code (customer types this)</label>
        <input type="text" name="code" value="{{ old('code') }}" placeholder="LAUNCH2026" required style="text-transform:uppercase">

        <label>Discount type</label>
        <select name="discount_type" required>
            <option value="percent" {{ old('discount_type') === 'percent' ? 'selected' : '' }}>Percent off</option>
            <option value="amount" {{ old('discount_type') === 'amount' ? 'selected' : '' }}>Fixed amount off (USD)</option>
        </select>

        <label>Discount value</label>
        <input type="number" name="discount_value" min="1" value="{{ old('discount_value') }}" placeholder="e.g. 20 for 20%, or 500 for $5.00 (cents)" required>
        <div class="hint">Percent: whole number 1-100. Amount: cents (500 = $5.00).</div>

        <label>Duration</label>
        <select name="duration" id="durationSelect" required onchange="document.getElementById('monthsField').style.display = this.value === 'repeating' ? 'block' : 'none'">
            <option value="once" {{ old('duration') === 'once' ? 'selected' : '' }}>Once — first charge only, full price after</option>
            <option value="repeating" {{ old('duration', 'repeating') === 'repeating' ? 'selected' : '' }}>For a period, then full price resumes</option>
            <option value="forever" {{ old('duration') === 'forever' ? 'selected' : '' }}>Forever — every renewal</option>
        </select>

        <div id="monthsField">
            <label>Discount period</label>
            <select name="duration_in_months">
                <option value="1" {{ old('duration_in_months') === '1' ? 'selected' : '' }}>1 month</option>
                <option value="12" {{ old('duration_in_months', '12') === '12' ? 'selected' : '' }}>1 year (12 months)</option>
            </select>
            <div class="hint">After this many billing cycles, Stripe automatically resumes charging the full recurring price — nothing else to do.</div>
        </div>

        <label>Max redemptions (blank = unlimited)</label>
        <input type="number" name="max_redemptions" min="1" value="{{ old('max_redemptions') }}">

        <label>Expires at (blank = never)</label>
        <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}">

        <button type="submit" class="btn" style="margin-top:16px">Create promo code</button>
    </form>
    <script>document.getElementById('monthsField').style.display = document.getElementById('durationSelect').value === 'repeating' ? 'block' : 'none';</script>
@endsection
