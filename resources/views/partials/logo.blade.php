{{-- Real Tillora brand mark (Stitch-generated). $dark=true uses the white-on-slate variant for dark surfaces; $iconOnly=true shows just the favicon mark (for compact spaces). --}}
@php($size = $size ?? 32)
@if ($iconOnly ?? false)
    <img src="{{ asset('images/favicon.svg') }}" alt="Tillora" width="{{ $size }}" height="{{ $size }}" style="border-radius:{{ $size * 0.22 }}px">
@else
    <img src="{{ asset($dark ?? false ? 'images/logo-dark.svg' : 'images/logo.svg') }}" alt="Tillora — Retail Cloud POS" height="{{ $size }}" style="width:auto;display:block">
@endif
