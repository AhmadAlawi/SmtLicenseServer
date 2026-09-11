<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Set up your shop — Tillora</title>
<link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&amp;family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&amp;display=swap" rel="stylesheet"/>
<style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}}::-webkit-scrollbar{display:none;}</style>
<script src="https://cdn.tailwindcss.com"></script>
<script id="tailwind-config">tailwind.config = { darkMode: 'class', theme: { extend: { colors: { 'inverse-primary': '#84dd00', 'secondary': '#3e6560', 'outline': '#707b62', 'on-tertiary': '#ffffff', 'on-secondary': '#ffffff', 'surface-dim': '#d8dbd5', 'tertiary-container': '#ffded7', 'on-secondary-container': '#426a64', 'on-error-container': '#93000a', 'secondary-container': '#bee8e1', 'error-container': '#ffdad6', 'surface-variant': '#e0e3de', 'on-secondary-fixed': '#00201d', 'tertiary-fixed': '#ffdad3', 'secondary-fixed-dim': '#a5cfc8', 'inverse-on-surface': '#eff2ec', 'on-tertiary-fixed-variant': '#5b403b', 'surface-container-lowest': '#ffffff', 'surface-tint': '#3c6a00', 'on-surface-variant': '#404a34', 'on-surface': '#191d19', 'primary-container': '#9cff1e', 'secondary-fixed': '#c1ebe4', 'outline-variant': '#bfcbae', 'surface-container-low': '#f2f5ef', 'surface': '#f7faf4', 'on-tertiary-container': '#7d5f59', 'error': '#ba1a1a', 'primary-fixed-dim': '#84dd00', 'on-primary': '#ffffff', 'on-primary-fixed-variant': '#2c5000', 'primary': '#3c6a00', 'background': '#f7faf4', 'primary-fixed': '#99fc18', 'on-error': '#ffffff', 'on-background': '#191d19', 'on-secondary-fixed-variant': '#264d48', 'surface-container-highest': '#e0e3de', 'surface-container': '#ecefe9', 'surface-container-high': '#e6e9e3', 'tertiary-fixed-dim': '#e4beb6', 'on-tertiary-fixed': '#2b1612', 'surface-bright': '#f7faf4', 'on-primary-fixed': '#0f2000', 'tertiary': '#755751', 'on-primary-container': '#427300', 'inverse-surface': '#2d312e' }, borderRadius: { 'DEFAULT': '1rem', 'lg': '2rem', 'xl': '3rem', 'full': '9999px' }, spacing: { 'margin-mobile': '1rem', 'space-sm': '0.5rem', 'space-md': '1rem', 'space-lg': '1.5rem', 'space-xl': '2.5rem', 'space-xs': '0.25rem' }, fontFamily: { 'label-sm': ['Plus Jakarta Sans'], 'headline-sm': ['Plus Jakarta Sans'], 'headline-lg': ['Plus Jakarta Sans'], 'body-md': ['DM Sans'], 'label-lg': ['Plus Jakarta Sans'], 'body-sm': ['DM Sans'] }, fontSize: { 'label-sm': ['12px', { lineHeight: '16px', letterSpacing: '0.04em', fontWeight: '700' }], 'headline-sm': ['22px', { lineHeight: '28px', letterSpacing: '-0.015em', fontWeight: '600' }], 'body-md': ['15px', { lineHeight: '24px', letterSpacing: '-0.005em', fontWeight: '400' }], 'label-lg': ['14px', { lineHeight: '20px', letterSpacing: '0em', fontWeight: '600' }], 'headline-lg': ['32px', { lineHeight: '38px', letterSpacing: '-0.02em', fontWeight: '700' }], 'body-sm': ['13px', { lineHeight: '18px', letterSpacing: '0em', fontWeight: '400' }] } } } };</script>
</head>
<body class="bg-surface-container-low font-body-md text-body-md text-on-surface antialiased">
<main class="w-full min-h-screen bg-surface-container-low flex flex-col items-center justify-center p-space-md sm:p-space-xl">
<div class="flex flex-col w-full items-center justify-center py-space-md">
<div class="w-full max-w-2xl bg-surface-container-lowest rounded-xl shadow-xl p-space-md sm:p-space-xl relative overflow-hidden flex flex-col gap-space-lg">

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm pb-space-sm">
<div class="flex items-center gap-space-sm">@include('partials.logo', ['size' => 26])</div>
<div class="flex items-center gap-space-xs self-start sm:self-auto bg-surface-container-low px-space-sm py-1.5 rounded-full">
<span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
<span class="font-label-sm text-label-sm text-secondary font-semibold" id="stepCounterBadge">Step 1 of 5: Plan</span>
</div>
</div>

@if ($errors->any())
<div class="bg-error-container text-on-error-container rounded-lg p-space-sm font-body-sm text-body-sm">{{ $errors->first() }}</div>
@endif

<div class="flex flex-col gap-space-xs">
<div class="grid grid-cols-5 gap-2 w-full">
@for ($i = 1; $i <= 5; $i++)
<span class="step-segment h-2 rounded-full transition-all duration-300 {{ $i === 1 ? 'bg-primary-container' : 'bg-surface-container-highest' }}" id="seg-{{ $i }}"></span>
@endfor
</div>
</div>

<form method="POST" action="{{ route('signup.store') }}" enctype="multipart/form-data" id="wizard" class="contents">
@csrf

<div class="relative min-h-[380px] flex flex-col justify-center">

{{-- STEP 1: Plan --}}
<div class="step-panel flex flex-col gap-space-md" id="step-panel-1">
<div class="flex flex-col gap-1">
<h2 class="font-headline-lg text-headline-lg text-on-surface">Select your plan</h2>
<p class="font-body-md text-body-md text-secondary">You can change this later.</p>
</div>
<div class="grid grid-cols-1 gap-space-sm pt-space-xs">
@foreach ($plans as $plan)
<label class="plan-card flex items-center justify-between p-space-md rounded-lg bg-surface-container-low cursor-pointer transition-all hover:bg-surface-container">
<div class="flex items-center gap-space-sm">
<input class="w-5 h-5 accent-primary" name="plan" type="radio" value="{{ $plan->code }}" {{ old('plan', request('plan')) === $plan->code ? 'checked' : ($loop->first && old('plan', request('plan')) === null ? 'checked' : '') }} required>
<div class="flex flex-col">
<span class="font-label-lg text-label-lg text-on-surface font-bold">{{ $plan->name }}</span>
<span class="font-body-sm text-body-sm text-secondary">{{ $plan->seat_limit ? "Up to {$plan->seat_limit} team members" : 'Unlimited team members' }}</span>
</div>
</div>
<div class="text-right">
<span class="font-headline-sm text-headline-sm text-on-surface font-bold">{{ $plan->display_price ?: 'Contact us' }}</span>
@if ($plan->display_price)<span class="font-body-sm text-body-sm text-secondary block">/month</span>@endif
</div>
</label>
@endforeach
</div>
</div>

{{-- STEP 2: Shop details --}}
<div class="step-panel hidden flex-col gap-space-md" id="step-panel-2">
<div class="flex flex-col gap-1">
<h2 class="font-headline-lg text-headline-lg text-on-surface">Tell us about your shop</h2>
<p class="font-body-md text-body-md text-secondary">This becomes the name on your receipts and login screen.</p>
</div>
<div class="flex flex-col gap-space-md">
<div class="flex flex-col gap-space-xs">
<label class="font-label-lg text-label-lg text-on-surface font-semibold" for="company_name">Shop name</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-secondary">storefront</span>
<input class="w-full bg-surface-container-low focus:bg-surface-container-lowest text-on-surface rounded-full py-3.5 pl-12 pr-4 font-body-md text-body-md transition-all outline-none" id="company_name" name="company_name" placeholder="e.g. Haven Goods" type="text" value="{{ old('company_name') }}" required>
</div>
</div>
<div class="flex flex-col gap-space-xs">
<span class="font-label-lg text-label-lg text-on-surface font-semibold">Logo (optional)</span>
<div class="w-full p-space-md rounded-lg bg-surface-container-low hover:bg-surface-container transition-all cursor-pointer flex flex-col items-center justify-center text-center gap-space-xs group" onclick="document.getElementById('logo').click()">
<input accept="image/png,image/jpeg,image/svg+xml,image/webp" class="hidden" id="logo" name="logo" type="file">
<div class="w-12 h-12 rounded-full bg-surface-container-highest group-hover:bg-primary-container flex items-center justify-center transition-colors">
<span class="material-symbols-outlined text-secondary group-hover:text-on-primary-fixed-variant">cloud_upload</span>
</div>
<p class="font-label-lg text-label-lg text-on-surface font-semibold" id="dropzoneText">Click to upload your logo</p>
<span class="font-body-sm text-body-sm text-secondary">PNG, JPG, SVG, or WebP</span>
</div>
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-lg text-label-lg text-on-surface font-semibold" for="branch_count">How many branches do you have?</label>
<input class="w-full bg-surface-container-low text-on-surface rounded-full py-3.5 px-4 font-body-md text-body-md outline-none" id="branch_count" name="branch_count" type="number" min="1" max="500" value="{{ old('branch_count', 1) }}">
<span class="font-body-sm text-body-sm text-secondary">Just for sizing your plan — you'll set each branch up inside your admin panel.</span>
</div>
</div>
</div>

{{-- STEP 3: Subdomain --}}
<div class="step-panel hidden flex-col gap-space-md" id="step-panel-3">
<div class="flex flex-col gap-1">
<h2 class="font-headline-lg text-headline-lg text-on-surface">Claim your address</h2>
<p class="font-body-md text-body-md text-secondary">This is where your shop will live.</p>
</div>
<div class="flex flex-col gap-space-md pt-space-xs">
<div class="flex flex-col gap-space-xs">
<label class="font-label-lg text-label-lg text-on-surface font-semibold" for="subdomain">Subdomain</label>
<div class="flex items-center bg-surface-container-low rounded-full px-4 py-1.5 focus-within:bg-surface-container-lowest transition-all">
<span class="material-symbols-outlined text-secondary pr-2">dns</span>
<input class="bg-transparent font-headline-sm text-headline-sm text-on-surface outline-none w-full font-bold lowercase" id="subdomain" name="subdomain" type="text" value="{{ old('subdomain') }}" pattern="[a-z0-9]+(-[a-z0-9]+)*" minlength="3" maxlength="30" required>
<span class="font-headline-sm text-headline-sm text-secondary font-bold select-none pr-2">.{{ $rootDomain ?: 'tillora.app' }}</span>
</div>
</div>
<div class="flex items-center justify-between p-space-sm rounded-lg bg-surface-container-high">
<span class="font-label-lg text-label-lg text-on-surface font-bold" id="subdomainStatusText">Enter at least 3 characters to check availability</span>
</div>
</div>
</div>

{{-- STEP 4: Admin account --}}
<div class="step-panel hidden flex-col gap-space-md" id="step-panel-4">
<div class="flex flex-col gap-1">
<h2 class="font-headline-lg text-headline-lg text-on-surface">Create your admin account</h2>
<p class="font-body-md text-body-md text-secondary">You'll use this to log into your shop.</p>
</div>
<div class="flex flex-col gap-space-sm pt-space-xs">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm">
<div class="flex flex-col gap-1">
<label class="font-label-sm text-label-sm text-secondary uppercase tracking-wider" for="admin_name">Your name</label>
<input class="bg-surface-container-low text-on-surface rounded-full py-3 px-4 font-body-md text-body-md outline-none focus:bg-surface-container-lowest" id="admin_name" name="admin_name" placeholder="First and last name" type="text" value="{{ old('admin_name') }}" required>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-sm text-label-sm text-secondary uppercase tracking-wider" for="admin_email">Email (also your login)</label>
<input class="bg-surface-container-low text-on-surface rounded-full py-3 px-4 font-body-md text-body-md outline-none focus:bg-surface-container-lowest" id="admin_email" name="admin_email" placeholder="name@company.com" type="email" value="{{ old('admin_email') }}" required>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm">
<div class="flex flex-col gap-1">
<label class="font-label-sm text-label-sm text-secondary uppercase tracking-wider" for="admin_password">Password</label>
<input class="w-full bg-surface-container-low text-on-surface rounded-full py-3 px-4 font-body-md text-body-md outline-none focus:bg-surface-container-lowest" id="admin_password" name="admin_password" type="password" minlength="8" required>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-sm text-label-sm text-secondary uppercase tracking-wider" for="admin_password_confirmation">Confirm password</label>
<input class="w-full bg-surface-container-low text-on-surface rounded-full py-3 px-4 font-body-md text-body-md outline-none focus:bg-surface-container-lowest" id="admin_password_confirmation" name="admin_password_confirmation" type="password" minlength="8" required>
</div>
</div>
</div>
</div>

{{-- STEP 5: Review --}}
<div class="step-panel hidden flex-col gap-space-md" id="step-panel-5">
<div class="flex flex-col gap-1">
<h2 class="font-headline-lg text-headline-lg text-on-surface">Review &amp; confirm</h2>
<p class="font-body-md text-body-md text-secondary">We'll email you a confirmation link before payment.</p>
</div>
<div class="flex flex-col bg-surface-container-high rounded-lg p-space-md gap-space-sm">
<div class="flex items-center justify-between pb-space-xs">
<div class="flex items-center gap-space-sm">
<div class="w-12 h-12 rounded-lg bg-surface-container-lowest flex items-center justify-center text-primary"><span class="material-symbols-outlined text-headline-sm">point_of_sale</span></div>
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-on-surface font-bold" id="summaryShopName">—</span>
<span class="font-body-sm text-body-sm text-secondary" id="summaryBranches">—</span>
</div>
</div>
</div>
<div class="grid grid-cols-2 sm:grid-cols-3 gap-space-sm pt-space-xs bg-surface-container-lowest p-space-sm rounded-DEFAULT">
<div class="flex flex-col"><span class="font-label-sm text-label-sm uppercase tracking-wider text-secondary">Plan</span><span class="font-label-lg text-label-lg text-on-surface font-bold" id="summaryPlan">—</span></div>
<div class="flex flex-col"><span class="font-label-sm text-label-sm uppercase tracking-wider text-secondary">Address</span><span class="font-label-lg text-label-lg text-primary font-bold" id="summarySubdomain">—</span></div>
<div class="flex flex-col"><span class="font-label-sm text-label-sm uppercase tracking-wider text-secondary">Admin</span><span class="font-label-lg text-label-lg text-on-surface font-bold" id="summaryAdmin">—</span></div>
</div>
</div>
</div>

</div>

<div class="flex flex-col gap-space-sm pt-space-sm">
<div class="flex items-center justify-between gap-space-sm">
<button class="px-space-md py-3 rounded-full bg-surface-container-high hover:bg-surface-container-highest text-secondary font-label-lg text-label-lg font-bold transition-all flex items-center gap-1 opacity-40 pointer-events-none" id="prevBtn" type="button">
<span class="material-symbols-outlined text-label-lg">arrow_back</span><span>Back</span>
</button>
<button class="flex-1 sm:flex-initial px-space-xl py-3.5 rounded-full bg-primary-container hover:bg-inverse-primary text-on-secondary-fixed font-headline-sm text-headline-sm tracking-tight font-bold shadow-md hover:shadow-xl transition-all flex items-center justify-center gap-2" id="nextBtn" type="button">
<span id="nextBtnLabel">Continue</span><span class="material-symbols-outlined text-headline-sm">arrow_forward</span>
</button>
</div>
</div>

</form>
</div>
</div>
</main>
<script>
(function () {
    let step = 1;
    const total = 5;
    const labels = ['Plan', 'Shop Details', 'Address', 'Admin Account', 'Review & Confirm'];
    const nextLabels = ['Continue to Shop Details', 'Continue to Address', 'Continue to Admin Account', 'Review & Confirm', 'Confirm & Continue to Payment'];
    const form = document.getElementById('wizard');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    function panels() { return [...document.querySelectorAll('.step-panel')]; }

    function render() {
        panels().forEach(p => {
            const n = Number(p.id.split('-').pop());
            p.classList.toggle('hidden', n !== step);
            p.classList.toggle('flex', n === step);
        });
        for (let i = 1; i <= total; i++) {
            document.getElementById(`seg-${i}`).classList.toggle('bg-primary-container', i <= step);
            document.getElementById(`seg-${i}`).classList.toggle('bg-surface-container-highest', i > step);
        }
        document.getElementById('stepCounterBadge').textContent = `Step ${step} of ${total}: ${labels[step - 1]}`;
        prevBtn.classList.toggle('opacity-40', step === 1);
        prevBtn.classList.toggle('pointer-events-none', step === 1);
        document.getElementById('nextBtnLabel').textContent = nextLabels[step - 1];
        nextBtn.type = step === total ? 'submit' : 'button';
        if (step === total) renderSummary();
    }

    function currentPanelValid() {
        const panel = document.getElementById(`step-panel-${step}`);
        return [...panel.querySelectorAll('input')].every(i => i.checkValidity());
    }

    nextBtn.addEventListener('click', (e) => {
        if (!currentPanelValid()) { form.reportValidity(); return; }
        if (step < total) { e.preventDefault(); step++; render(); }
        // On the final step, type=submit and validity already checked — let the click submit for real.
    });
    prevBtn.addEventListener('click', () => { if (step > 1) { step--; render(); } });

    function renderSummary() {
        const val = (name) => form.querySelector(`[name="${name}"]`)?.value || '—';
        const planLabel = form.querySelector('input[name="plan"]:checked')?.closest('.plan-card')?.querySelector('.font-label-lg')?.textContent || '—';
        const logoFile = document.getElementById('logo').files[0];
        document.getElementById('summaryShopName').textContent = val('company_name');
        document.getElementById('summaryBranches').textContent = `${val('branch_count')} branch(es)` + (logoFile ? ` • ${logoFile.name}` : '');
        document.getElementById('summaryPlan').textContent = planLabel;
        document.getElementById('summarySubdomain').textContent = `${val('subdomain')}.{{ $rootDomain ?: "tillora.app" }}`;
        document.getElementById('summaryAdmin').textContent = `${val('admin_name')} — ${val('admin_email')}`;
    }

    document.getElementById('logo').addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) document.getElementById('dropzoneText').textContent = file.name;
    });

    let timer;
    document.getElementById('subdomain').addEventListener('input', function () {
        clearTimeout(timer);
        const status = document.getElementById('subdomainStatusText');
        const value = this.value.trim();
        if (value.length < 3) { status.textContent = 'Enter at least 3 characters to check availability'; return; }
        timer = setTimeout(async () => {
            try {
                const res = await fetch(`{{ route('signup.check-subdomain') }}?subdomain=${encodeURIComponent(value)}`);
                const data = await res.json();
                status.textContent = !data.valid
                    ? 'Only lowercase letters, numbers, and hyphens'
                    : (data.available ? `${value}.{{ $rootDomain ?: "tillora.app" }} is available!` : 'Already taken');
            } catch (e) { /* server re-validates on submit regardless */ }
        }, 400);
    });

    render();
})();
</script>
</body>
</html>
