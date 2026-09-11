{{-- Placeholder Tillora mark — swap for the real Stitch-generated logo asset once available. Inline SVG so it needs no separate file and recolors via currentColor where used on dark backgrounds. --}}
@php($size = $size ?? 28)
<span style="display:inline-flex;align-items:center;gap:8px;font-weight:700;font-size:{{ $size * 0.64 }}px;color:{{ $dark ?? false ? '#fff' : '#1a1a2e' }}">
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0">
        <rect width="32" height="32" rx="8" fill="url(#tillora-grad)"/>
        <path d="M9 11h14M16 11v10" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
        <defs>
            <linearGradient id="tillora-grad" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
                <stop stop-color="#1a1a2e"/>
                <stop offset="1" stop-color="#0d9488"/>
            </linearGradient>
        </defs>
    </svg>
    Tillora
</span>
