<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
@include('partials.seo-meta', [
    'title' => 'Tillora — Retail Cloud POS',
    'description' => 'Self-serve multi-branch retail POS. Pick a plan, sign up online, and your own branded instance is live in minutes — no sales call.',
])
<link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&amp;family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&amp;display=swap" rel="stylesheet"/>
<style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}</style>
<script src="https://cdn.tailwindcss.com"></script>
<script id="tailwind-config">tailwind.config = { darkMode: 'class', theme: { extend: { colors: { 'inverse-primary': '#84dd00', 'secondary': '#3e6560', 'outline': '#707b62', 'on-tertiary': '#ffffff', 'on-secondary': '#ffffff', 'surface-dim': '#d8dbd5', 'tertiary-container': '#ffded7', 'on-secondary-container': '#426a64', 'on-error-container': '#93000a', 'secondary-container': '#bee8e1', 'error-container': '#ffdad6', 'surface-variant': '#e0e3de', 'on-secondary-fixed': '#00201d', 'tertiary-fixed': '#ffdad3', 'secondary-fixed-dim': '#a5cfc8', 'inverse-on-surface': '#eff2ec', 'on-tertiary-fixed-variant': '#5b403b', 'surface-container-lowest': '#ffffff', 'surface-tint': '#3c6a00', 'on-surface-variant': '#404a34', 'on-surface': '#191d19', 'primary-container': '#9cff1e', 'secondary-fixed': '#c1ebe4', 'outline-variant': '#bfcbae', 'surface-container-low': '#f2f5ef', 'surface': '#f7faf4', 'on-tertiary-container': '#7d5f59', 'error': '#ba1a1a', 'primary-fixed-dim': '#84dd00', 'on-primary': '#ffffff', 'on-primary-fixed-variant': '#2c5000', 'primary': '#3c6a00', 'background': '#f7faf4', 'primary-fixed': '#99fc18', 'on-error': '#ffffff', 'on-background': '#191d19', 'on-secondary-fixed-variant': '#264d48', 'surface-container-highest': '#e0e3de', 'surface-container': '#ecefe9', 'surface-container-high': '#e6e9e3', 'tertiary-fixed-dim': '#e4beb6', 'on-tertiary-fixed': '#2b1612', 'surface-bright': '#f7faf4', 'on-primary-fixed': '#0f2000', 'tertiary': '#755751', 'on-primary-container': '#427300', 'inverse-surface': '#2d312e' }, borderRadius: { 'DEFAULT': '1rem', 'lg': '2rem', 'xl': '3rem', 'full': '9999px' }, spacing: { 'margin-mobile': '1rem', 'space-sm': '0.5rem', 'space-md': '1rem', 'space-lg': '1.5rem', 'space-xl': '2.5rem', 'space-xs': '0.25rem', 'gutter-mobile': '0.75rem', 'margin': '3rem', 'gutter': '1.5rem' }, fontFamily: { 'label-sm': ['Plus Jakarta Sans'], 'headline-sm': ['Plus Jakarta Sans'], 'headline-xl': ['Plus Jakarta Sans'], 'display-hero': ['Plus Jakarta Sans'], 'body-md': ['DM Sans'], 'display-hero-mobile': ['Plus Jakarta Sans'], 'label-lg': ['Plus Jakarta Sans'], 'headline-lg': ['Plus Jakarta Sans'], 'body-sm': ['DM Sans'], 'body-lg': ['DM Sans'], 'headline-xl-mobile': ['Plus Jakarta Sans'] }, fontSize: { 'label-sm': ['12px', { lineHeight: '16px', letterSpacing: '0.04em', fontWeight: '700' }], 'headline-sm': ['22px', { lineHeight: '28px', letterSpacing: '-0.015em', fontWeight: '600' }], 'headline-xl': ['48px', { lineHeight: '54px', letterSpacing: '-0.025em', fontWeight: '700' }], 'display-hero': ['72px', { lineHeight: '76px', letterSpacing: '-0.03em', fontWeight: '800' }], 'body-md': ['15px', { lineHeight: '24px', letterSpacing: '-0.005em', fontWeight: '400' }], 'display-hero-mobile': ['40px', { lineHeight: '44px', letterSpacing: '-0.03em', fontWeight: '800' }], 'label-lg': ['14px', { lineHeight: '20px', letterSpacing: '0em', fontWeight: '600' }], 'headline-lg': ['32px', { lineHeight: '38px', letterSpacing: '-0.02em', fontWeight: '700' }], 'body-sm': ['13px', { lineHeight: '18px', letterSpacing: '0em', fontWeight: '400' }], 'body-lg': ['18px', { lineHeight: '28px', letterSpacing: '-0.01em', fontWeight: '400' }], 'headline-xl-mobile': ['32px', { lineHeight: '38px', letterSpacing: '-0.02em', fontWeight: '700' }] } } } };</script>
@include('partials.analytics')
</head>
<body class="bg-surface-container-low font-body-md text-body-md text-on-surface antialiased">

<header class="fixed top-0 w-full z-50 bg-surface-container-low/85 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
<div class="h-20 max-w-7xl mx-auto px-margin-mobile lg:px-margin flex items-center justify-between gap-gutter">
<div class="flex items-center gap-space-md">@include('partials.logo', ['size' => 28])</div>
<nav class="hidden lg:flex items-center gap-space-xs bg-surface-container/60 p-space-xs rounded-full">
<a class="px-space-md py-space-sm font-label-lg text-label-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all rounded-full" href="#features">Features</a>
<a class="px-space-md py-space-sm rounded-full font-label-lg text-label-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all" href="#how-it-works">How it Works</a>
<a class="px-space-md py-space-sm rounded-full font-label-lg text-label-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all" href="https://demo.sphereofthesun.com/login" target="_blank" rel="noopener">Demo</a>
<a class="px-space-md py-space-sm rounded-full font-label-lg text-label-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all" href="#pricing">Pricing</a>
<a class="px-space-md py-space-sm rounded-full font-label-lg text-label-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all" href="#faq">FAQ</a>
</nav>
<div class="flex items-center gap-space-md">
<select id="currencySelect" aria-label="Currency" class="hidden sm:block rounded-full border border-outline-variant bg-surface-container-lowest px-space-sm py-1.5 font-label-lg text-label-lg text-on-surface-variant">
<option value="USD">USD</option>
@foreach ($currencies as $currency)
<option value="{{ $currency }}">{{ $currency }}</option>
@endforeach
</select>
<a class="hidden sm:inline-flex px-space-md py-space-sm rounded-full font-label-lg text-label-lg text-on-surface-variant hover:text-on-surface transition-colors" href="{{ route('login') }}">Staff Login</a>
<a class="inline-flex items-center justify-center px-space-lg py-space-sm rounded-full bg-primary-container text-on-secondary-fixed font-label-lg text-label-lg font-semibold hover:bg-primary-fixed-dim transition-all shadow-[0_0_24px_0_rgba(156,255,30,0.35)]" href="{{ route('signup.index') }}">Get Started</a>
</div>
</div>
</header>

<main class="w-full pt-20 bg-surface-container-low min-h-screen">
<div class="flex flex-col w-full">
<div class="relative w-full overflow-hidden">
<div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[980px] h-[480px] bg-gradient-to-b from-primary-container/25 via-secondary-container/10 to-transparent blur-3xl pointer-events-none rounded-full"></div>

{{-- HERO --}}
<section class="relative max-w-7xl mx-auto px-margin-mobile lg:px-margin pt-10 pb-20 lg:pt-16 lg:pb-28">
<div class="flex flex-col items-center text-center">
<div class="inline-flex items-center gap-space-sm px-space-md py-space-xs rounded-full bg-surface-container-lowest shadow-sm mb-space-lg">
<span class="w-2 h-2 rounded-full bg-primary-container animate-pulse"></span>
<span class="font-label-sm text-label-sm text-secondary tracking-wider uppercase">Multi-Branch Cloud POS</span>
</div>
<h1 class="font-display-hero text-display-hero-mobile lg:text-display-hero text-on-secondary-fixed font-extrabold tracking-tight max-w-5xl leading-[1.05] text-balance">
One till to rule every store. Real-time retail without limits.
</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mt-space-lg mb-space-xl text-balance">
Unify checkout, inventory, and reporting across every branch you run — offline-ready, and live in minutes, not weeks.
</p>
<div class="flex flex-col sm:flex-row items-center gap-space-md w-full sm:w-auto">
<a class="w-full sm:w-auto inline-flex items-center justify-center gap-space-sm px-space-xl py-4 rounded-full bg-primary-container text-on-secondary-fixed font-label-lg text-label-lg font-bold hover:bg-primary-fixed-dim transition-all shadow-[0_0_28px_0_rgba(156,255,30,0.45)] active:scale-95" href="{{ route('signup.index') }}">
<span>Start Your Setup</span>
<span class="material-symbols-outlined text-[20px]">arrow_forward</span>
</a>
<a class="w-full sm:w-auto inline-flex items-center justify-center gap-space-sm px-space-xl py-4 rounded-full bg-surface-container-lowest text-on-surface font-label-lg text-label-lg font-semibold hover:bg-surface-container transition-all shadow-sm active:scale-95" href="https://demo.sphereofthesun.com/login" target="_blank" rel="noopener">
<span class="material-symbols-outlined text-secondary text-[20px]">play_circle</span>
<span>Try Live Demo</span>
</a>
</div>

<div class="mt-space-xl pt-space-lg flex flex-col items-center">
<p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider mb-space-md">Built for growing multi-branch retail teams</p>
</div>

{{-- Illustrative product preview — sample data, not a real customer's live figures --}}
<div class="w-full mt-14 relative" id="product-preview">
<div class="absolute inset-0 bg-primary-container/20 rounded-3xl filter blur-2xl transform scale-95 pointer-events-none"></div>
<div class="relative bg-surface-container-lowest rounded-3xl shadow-xl overflow-hidden text-left">
<div class="bg-surface-container-high px-space-lg py-3 flex items-center justify-between">
<div class="flex items-center gap-space-sm">
<span class="w-3 h-3 rounded-full bg-error"></span>
<span class="w-3 h-3 rounded-full bg-primary-fixed-dim"></span>
<span class="w-3 h-3 rounded-full bg-primary-container"></span>
<span class="ml-2 font-label-sm text-label-sm text-on-surface-variant font-medium">Tillora Register — Example Preview</span>
</div>
<span class="inline-flex items-center gap-1 font-label-sm text-label-sm text-secondary font-semibold bg-surface-container-lowest px-3 py-1 rounded-full">
<span class="w-2 h-2 rounded-full bg-primary-container"></span> Online
</span>
</div>
<div class="p-6 lg:p-8 grid grid-cols-1 lg:grid-cols-12 gap-6 bg-surface-container-lowest">
<div class="lg:col-span-8 flex flex-col gap-6">
<div class="bg-surface-container-low rounded-2xl p-6 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
<div>
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider block">Today's Sales (Example)</span>
<div class="flex items-baseline gap-3 mt-1">
<span class="font-headline-xl text-headline-xl font-bold text-on-secondary-fixed tracking-tight">$4,289.50</span>
<span class="inline-flex items-center text-primary font-label-lg text-label-lg font-bold"><span class="material-symbols-outlined text-[18px]">trending_up</span> +12%</span>
</div>
</div>
<div class="flex items-center gap-2">
<div class="px-4 py-2 bg-surface-container-lowest rounded-full shadow-sm text-center">
<div class="font-headline-sm text-headline-sm font-bold text-on-surface">142</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Orders</div>
</div>
</div>
</div>
<div>
<div class="flex items-center justify-between mb-3">
<h2 class="font-label-lg text-label-lg font-bold text-on-surface uppercase tracking-wide">Branches</h2>
</div>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
@foreach (['Downtown', 'Mall Branch', 'Airport Kiosk'] as $branchName)
<div class="p-4 rounded-2xl bg-surface-container-lowest shadow-sm">
<div class="flex items-center justify-between">
<span class="font-label-lg text-label-lg font-bold text-on-surface">{{ $branchName }}</span>
<span class="inline-flex items-center gap-1 text-[11px] font-bold text-on-secondary-fixed bg-primary-container/60 px-2 py-0.5 rounded-full">
<span class="w-1.5 h-1.5 rounded-full bg-primary"></span> ACTIVE
</span>
</div>
</div>
@endforeach
</div>
</div>
<div class="bg-surface-container-low p-4 rounded-2xl flex items-center justify-between gap-4">
<div class="flex items-center gap-3 flex-1">
<span class="material-symbols-outlined text-secondary text-[26px]">barcode_scanner</span>
<span class="bg-surface-container-lowest px-4 py-2 rounded-full w-full font-body-md text-body-md text-on-surface shadow-sm">Scan SKU or search product…</span>
</div>
</div>
</div>
<div class="lg:col-span-4 bg-surface-container rounded-2xl p-5 flex flex-col justify-between">
<div>
<div class="flex items-center justify-between pb-3">
<span class="font-label-lg text-label-lg font-bold text-on-surface">Current Cart</span>
<span class="px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm font-bold">2 Items</span>
</div>
<div class="space-y-2 mt-2">
<div class="bg-surface-container-lowest p-3 rounded-xl flex items-center justify-between shadow-sm">
<p class="font-label-lg text-label-lg font-bold text-on-surface">Item A</p>
<span class="font-label-lg text-label-lg font-bold text-on-surface">$24.00</span>
</div>
<div class="bg-surface-container-lowest p-3 rounded-xl flex items-center justify-between shadow-sm">
<p class="font-label-lg text-label-lg font-bold text-on-surface">Item B</p>
<span class="font-label-lg text-label-lg font-bold text-on-surface">$12.00</span>
</div>
</div>
</div>
<div class="mt-6 pt-4 bg-surface-container-lowest rounded-xl p-4 shadow-sm">
<div class="flex justify-between font-headline-sm text-headline-sm font-bold text-on-secondary-fixed mb-4">
<span>Total Due</span><span>$36.00</span>
</div>
<button class="w-full py-3 rounded-full bg-primary-container text-on-secondary-fixed font-label-lg text-label-lg font-bold shadow-[0_0_18px_rgba(156,255,30,0.35)]" type="button">
<span class="material-symbols-outlined text-[20px] align-middle">contactless</span> Pay
</button>
</div>
</div>
</div>
</div>
</div>
</div>
</section>

{{-- FEATURES --}}
<section id="features" class="max-w-7xl mx-auto px-margin-mobile lg:px-margin py-20">
<div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
<div>
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm font-bold uppercase tracking-wider mb-3">Built for Retail</div>
<h2 class="font-headline-xl text-headline-xl-mobile lg:text-headline-xl text-on-secondary-fixed font-bold tracking-tight">Everything a growing retail chain needs.</h2>
</div>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-md">One system for checkout, stock, and staff — not five disconnected tools.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">

<div class="bg-surface-container-lowest rounded-3xl p-8 lg:p-10 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
<div>
<div class="w-14 h-14 rounded-2xl bg-secondary-container flex items-center justify-center text-on-secondary-container mb-6 group-hover:bg-primary-container group-hover:text-on-secondary-fixed transition-colors"><span class="material-symbols-outlined text-[32px]">hub</span></div>
<h3 class="font-headline-lg text-headline-lg text-on-secondary-fixed font-bold mb-3">Multi-Branch Management</h3>
<p class="font-body-lg text-body-lg text-on-surface-variant">Run checkout, stock, and staff across every location from a single admin panel — add branches whenever your business grows.</p>
</div>
</div>

<div class="bg-surface-container-lowest rounded-3xl p-8 lg:p-10 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
<div>
<div class="w-14 h-14 rounded-2xl bg-secondary-container flex items-center justify-center text-on-secondary-container mb-6 group-hover:bg-primary-container group-hover:text-on-secondary-fixed transition-colors"><span class="material-symbols-outlined text-[32px]">sync_saved_locally</span></div>
<h3 class="font-headline-lg text-headline-lg text-on-secondary-fixed font-bold mb-3">Real-Time Inventory</h3>
<p class="font-body-lg text-body-lg text-on-surface-variant">Stock levels update the moment a sale happens across every register — no end-of-day reconciliation, no nightly batch jobs.</p>
</div>
</div>

<div class="bg-surface-container-lowest rounded-3xl p-8 lg:p-10 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
<div>
<div class="w-14 h-14 rounded-2xl bg-secondary-container flex items-center justify-center text-on-secondary-container mb-6 group-hover:bg-primary-container group-hover:text-on-secondary-fixed transition-colors"><span class="material-symbols-outlined text-[32px]">wifi_off</span></div>
<h3 class="font-headline-lg text-headline-lg text-on-secondary-fixed font-bold mb-3">Offline-Ready Checkout</h3>
<p class="font-body-lg text-body-lg text-on-surface-variant">Never turn away a shopper because the internet dropped. The cashier screen keeps ringing up sales locally and syncs automatically once you're back online.</p>
</div>
</div>

<div class="bg-surface-container-lowest rounded-3xl p-8 lg:p-10 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
<div>
<div class="w-14 h-14 rounded-2xl bg-secondary-container flex items-center justify-center text-on-secondary-container mb-6 group-hover:bg-primary-container group-hover:text-on-secondary-fixed transition-colors"><span class="material-symbols-outlined text-[32px]">receipt_long</span></div>
<h3 class="font-headline-lg text-headline-lg text-on-secondary-fixed font-bold mb-3">Receipts, Barcodes &amp; Full Accounting</h3>
<p class="font-body-lg text-body-lg text-on-surface-variant">Thermal receipts, product barcodes, and shelf labels out of the box — plus a real chart of accounts and financial reports, not a bolt-on spreadsheet.</p>
</div>
</div>

</div>
</section>

{{-- HOW IT WORKS --}}
<section id="how-it-works" class="bg-surface-container py-24">
<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin">
<div class="text-center max-w-3xl mx-auto mb-16">
<span class="inline-block font-label-sm text-label-sm font-bold text-secondary uppercase tracking-wider mb-2">Zero Complicated Setup</span>
<h2 class="font-headline-xl text-headline-xl-mobile lg:text-headline-xl font-bold text-on-secondary-fixed tracking-tight">Live in minutes, not weeks.</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant mt-4">Pick a plan, tell us about your shop, and your own instance is ready to log into.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
<div class="bg-surface-container-lowest rounded-3xl p-8 shadow-sm flex flex-col justify-between relative overflow-hidden">
<div class="absolute -right-4 -top-4 font-display-hero text-surface-container font-black opacity-40 select-none">01</div>
<div>
<div class="w-12 h-12 rounded-full bg-primary-container text-on-secondary-fixed font-headline-sm text-headline-sm font-extrabold flex items-center justify-center mb-6">1</div>
<h3 class="font-headline-sm text-headline-sm text-on-secondary-fixed font-bold mb-3">Pick your plan</h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-6">Choose the tier that fits how many people and branches you run — change it later any time.</p>
</div>
</div>
<div class="bg-surface-container-lowest rounded-3xl p-8 shadow-sm flex flex-col justify-between relative overflow-hidden">
<div class="absolute -right-4 -top-4 font-display-hero text-surface-container font-black opacity-40 select-none">02</div>
<div>
<div class="w-12 h-12 rounded-full bg-primary-container text-on-secondary-fixed font-headline-sm text-headline-sm font-extrabold flex items-center justify-center mb-6">2</div>
<h3 class="font-headline-sm text-headline-sm text-on-secondary-fixed font-bold mb-3">Tell us about your shop</h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-6">Shop name, logo, branch count, your subdomain, and your own admin login — a few short steps.</p>
</div>
</div>
<div class="bg-surface-container-lowest rounded-3xl p-8 shadow-sm flex flex-col justify-between relative overflow-hidden">
<div class="absolute -right-4 -top-4 font-display-hero text-surface-container font-black opacity-40 select-none">03</div>
<div>
<div class="w-12 h-12 rounded-full bg-primary-container text-on-secondary-fixed font-headline-sm text-headline-sm font-extrabold flex items-center justify-center mb-6">3</div>
<h3 class="font-headline-sm text-headline-sm text-on-secondary-fixed font-bold mb-3">Start selling</h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-6">Your own Tillora instance is provisioned automatically — log in and ring up your first sale.</p>
</div>
</div>
</div>
</div>
</section>

{{-- PRICING --}}
<section id="pricing" class="max-w-7xl mx-auto px-margin-mobile lg:px-margin py-24">
<div class="text-center max-w-2xl mx-auto mb-16">
<span class="font-label-sm text-label-sm font-bold text-secondary uppercase tracking-wider mb-2 block">Simple, Predictable Pricing</span>
<h2 class="font-headline-xl text-headline-xl-mobile lg:text-headline-xl font-bold text-on-secondary-fixed tracking-tight">Plans that scale with your team.</h2>
</div>
@if ($plans->isEmpty())
<p class="text-center text-on-surface-variant">No plans are configured for online purchase yet.</p>
@else
<div class="grid grid-cols-1 lg:grid-cols-{{ min(3, $plans->count()) }} gap-8 items-stretch">
@foreach ($plans as $plan)
@php($featured = $plans->count() >= 3 && $loop->index === intdiv($plans->count() - 1, 2))
<div class="bg-surface-container-lowest rounded-3xl p-8 lg:p-10 shadow-{{ $featured ? 'xl' : 'sm' }} flex flex-col justify-between relative {{ $featured ? 'transform lg:-translate-y-4' : '' }}">
@if ($featured)
<div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-primary-container text-on-secondary-fixed px-4 py-1 rounded-full font-label-sm text-label-sm font-extrabold tracking-wide uppercase shadow-md flex items-center gap-1.5">
<span class="material-symbols-outlined text-[14px]">star</span> Most Popular
</div>
@endif
<div>
<div class="mb-6">
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">{{ $plan->name }}</h3>
</div>
<div class="flex items-baseline gap-1 mb-8 plan-price" data-prices="{{ json_encode($plan->priceTable()) }}">
<span class="font-display-hero text-headline-xl font-black text-on-secondary-fixed price-amount">{{ $plan->display_price ?: 'Contact us' }}</span>
@if ($plan->display_price)<span class="font-body-md text-body-md text-on-surface-variant">/month</span>@endif
</div>
<ul class="space-y-4 font-body-md text-body-md text-on-surface-variant mb-8">
<li class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary text-[20px]">check</span>
<span>{{ $plan->seat_limit ? "Up to {$plan->seat_limit} team members" : 'Unlimited team members' }}</span>
</li>
@foreach (collect($plan->features ?? [])->filter() as $key => $enabled)
<li class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary text-[20px]">check</span>
<span>{{ str($key)->replace('_', ' ')->title() }}</span>
</li>
@endforeach
</ul>
</div>
<a class="w-full py-4 rounded-full {{ $featured ? 'bg-primary-container text-on-secondary-fixed shadow-[0_0_24px_rgba(156,255,30,0.45)] hover:bg-primary-fixed-dim' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }} font-label-lg text-label-lg font-bold text-center transition-all" href="{{ route('signup.index', ['plan' => $plan->code]) }}">
Choose {{ $plan->name }}
</a>
</div>
@endforeach
</div>
@endif
</section>

{{-- FAQ --}}
<section id="faq" class="max-w-4xl mx-auto px-margin-mobile lg:px-margin py-20">
<div class="text-center mb-14">
<span class="font-label-sm text-label-sm font-bold text-secondary uppercase tracking-wider mb-2 block">Everything You Need To Know</span>
<h2 class="font-headline-xl text-headline-xl-mobile lg:text-headline-xl font-bold text-on-secondary-fixed tracking-tight">Frequently Asked Questions</h2>
</div>
<div class="space-y-4">

<details class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm group">
<summary class="flex items-center justify-between cursor-pointer list-none">
<h3 class="font-headline-sm text-[18px] lg:text-[20px] font-bold text-on-surface">What happens at checkout if our internet goes down?</h3>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-45">add</span>
</summary>
<p class="font-body-md text-body-md text-on-surface-variant pt-4">The cashier screen keeps working offline — scans, discounts, and receipts queue locally and sync automatically the moment your connection is back.</p>
</details>

<details class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm group">
<summary class="flex items-center justify-between cursor-pointer list-none">
<h3 class="font-headline-sm text-[18px] lg:text-[20px] font-bold text-on-surface">Can we import our existing product catalog?</h3>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-45">add</span>
</summary>
<p class="font-body-md text-body-md text-on-surface-variant pt-4">Yes — products, categories, and barcodes can be imported once your instance is set up. Reach out to support if you need help with a large catalog migration.</p>
</details>

<details class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm group">
<summary class="flex items-center justify-between cursor-pointer list-none">
<h3 class="font-headline-sm text-[18px] lg:text-[20px] font-bold text-on-surface">Do we need special hardware?</h3>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-45">add</span>
</summary>
<p class="font-body-md text-body-md text-on-surface-variant pt-4">No proprietary lock-in — Tillora runs on standard tablets and desktops, and works with common USB/Bluetooth thermal receipt printers and barcode scanners.</p>
</details>

<details class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm group">
<summary class="flex items-center justify-between cursor-pointer list-none">
<h3 class="font-headline-sm text-[18px] lg:text-[20px] font-bold text-on-surface">How many branches can we add?</h3>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-45">add</span>
</summary>
<p class="font-body-md text-body-md text-on-surface-variant pt-4">As many as you need — branch count isn't capped, you add them from your admin panel any time. Plans differ mainly by how many team members can log in.</p>
</details>

<details class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm group">
<summary class="flex items-center justify-between cursor-pointer list-none">
<h3 class="font-headline-sm text-[18px] lg:text-[20px] font-bold text-on-surface">Can I change plans later?</h3>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-45">add</span>
</summary>
<p class="font-body-md text-body-md text-on-surface-variant pt-4">Yes — contact support and we'll move you to a different tier. Your data and setup stay exactly as they are.</p>
</details>

</div>
</section>

{{-- CLOSING CTA --}}
<section class="max-w-7xl mx-auto px-margin-mobile lg:px-margin pb-24">
<div class="bg-on-secondary-fixed rounded-3xl p-10 lg:p-16 text-center relative overflow-hidden shadow-2xl">
<div class="absolute -bottom-24 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-primary-container/20 blur-3xl pointer-events-none rounded-full"></div>
<div class="relative z-10 max-w-3xl mx-auto flex flex-col items-center">
<h2 class="font-headline-xl text-headline-xl-mobile lg:text-headline-xl font-extrabold text-surface tracking-tight leading-tight">Ready to replace your clunky legacy register?</h2>
<p class="font-body-lg text-body-lg text-surface-variant max-w-xl mt-4 mb-8">Set up your own Tillora instance today — live in minutes.</p>
<div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
<a class="w-full sm:w-auto px-space-xl py-4 rounded-full bg-primary-container text-on-secondary-fixed font-label-lg text-label-lg font-bold shadow-[0_0_32px_rgba(156,255,30,0.5)] hover:bg-primary-fixed-dim transition-all active:scale-95 text-center" href="{{ route('signup.index') }}">Get Started</a>
</div>
<div class="mt-8 flex items-center gap-4 text-surface-variant font-body-sm text-body-sm flex-wrap justify-center">
<span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-primary-container text-[18px]">bolt</span> Ready in minutes</span>
<span>•</span>
<span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-primary-container text-[18px]">verified</span> Cancel anytime</span>
</div>
</div>
</div>
</section>

</div>
</div>
</main>

<div class="bg-surface-container-high py-16">
<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin">
<div class="grid grid-cols-2 md:grid-cols-4 gap-10 pb-12">
<div class="col-span-2">
<div class="mb-4">@include('partials.logo', ['size' => 24])</div>
<p class="font-body-md text-body-md text-on-surface-variant max-w-sm">The multi-branch retail cloud POS — unifying checkout, offline reliability, and inventory across every store you run.</p>
</div>
<div>
<h4 class="font-label-lg text-label-lg font-bold text-on-surface mb-4">Product</h4>
<ul class="space-y-2.5 font-body-sm text-body-sm text-on-surface-variant">
<li><a class="hover:text-on-surface transition-colors" href="#features">Features</a></li>
<li><a class="hover:text-on-surface transition-colors" href="#pricing">Pricing</a></li>
<li><a class="hover:text-on-surface transition-colors" href="#faq">FAQ</a></li>
</ul>
</div>
<div>
<h4 class="font-label-lg text-label-lg font-bold text-on-surface mb-4">Company</h4>
<ul class="space-y-2.5 font-body-sm text-body-sm text-on-surface-variant">
<li><a class="hover:text-on-surface transition-colors" href="{{ route('signup.index') }}">Get Started</a></li>
<li><a class="hover:text-on-surface transition-colors" href="{{ route('login') }}">Staff Login</a></li>
</ul>
</div>
</div>
<div class="pt-8 border-t border-outline-variant/30 text-center font-body-sm text-body-sm text-on-surface-variant">
&copy; {{ date('Y') }} Tillora. All rights reserved.
</div>
</div>
</div>

<script>
(function () {
    const select = document.getElementById('currencySelect');
    if (!select) return;

    try {
        const stored = localStorage.getItem('tillora_currency');
        if (stored) select.value = stored;
    } catch (e) {}

    function apply() {
        const currency = select.value;
        document.querySelectorAll('.plan-price').forEach(el => {
            const prices = JSON.parse(el.dataset.prices || '{}');
            const amountEl = el.querySelector('.price-amount');
            if (amountEl && prices[currency]) amountEl.textContent = prices[currency];
        });
        try { localStorage.setItem('tillora_currency', currency); } catch (e) {}
    }

    select.addEventListener('change', apply);
    apply();
})();
</script>
</body>
</html>
