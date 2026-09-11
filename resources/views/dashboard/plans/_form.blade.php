@php($plan = $plan ?? null)
@if ($errors->any())
    <div class="flash" style="background:#fbe1e1">{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ $plan ? route('dashboard.plans.update', $plan) : route('dashboard.plans.store') }}" style="max-width:480px">
    @csrf
    @if ($plan) @method('PUT') @endif

    <label>Code</label>
    <input type="text" name="code" value="{{ old('code', $plan?->code) }}" placeholder="pro" required {{ $plan ? 'readonly' : '' }}>

    <label>Name</label>
    <input type="text" name="name" value="{{ old('name', $plan?->name) }}" placeholder="Pro" required>

    <label>Display price (marketing only — e.g. "$79/mo")</label>
    <input type="text" name="display_price" value="{{ old('display_price', $plan?->display_price) }}" placeholder="$79">
    <div class="hint">Shown on the landing page and wizard. The Stripe price ID below is what actually gets charged.</div>

    <label>Seat limit (blank = unlimited)</label>
    <input type="number" name="seat_limit" min="1" value="{{ old('seat_limit', $plan?->seat_limit) }}">

    <label>Stripe price ID</label>
    <input type="text" name="stripe_price_id" value="{{ old('stripe_price_id', $plan?->stripe_price_id) }}" placeholder="price_...">
    <div class="hint">Create the Product/Price in Stripe first, paste its ID here.</div>

    <label>Features</label>
    @foreach ($featureKeys as $key)
        <label style="font-weight:normal"><input type="checkbox" name="features[]" value="{{ $key }}"
            {{ in_array($key, old('features', array_keys(array_filter($plan?->features ?? [])))) ? 'checked' : '' }}>
            {{ str($key)->replace('_', ' ')->title() }}</label>
    @endforeach

    <label><input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan?->is_active ?? true) ? 'checked' : '' }}> Active (visible on the pricing page)</label>

    <button type="submit" class="btn" style="margin-top:16px">{{ $plan ? 'Save changes' : 'Create plan' }}</button>
</form>
