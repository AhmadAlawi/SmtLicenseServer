<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Tillora — A Self-Serve Alternative to Foodics for Multi-Branch Retail</title>
<meta name="description" content="Looking for a Foodics alternative? Tillora gives multi-branch retail shops transparent pricing, instant self-serve signup, and a private instance per store — no sales call required.">
<link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&amp;family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&amp;display=swap" rel="stylesheet"/>
<style>@layer base{html,body{margin:0;padding:0;}}::-webkit-scrollbar{display:none;}</style>
<script src="https://cdn.tailwindcss.com"></script>
<script id="tailwind-config">tailwind.config = { theme: { extend: { colors: { 'primary-container': '#9cff1e', 'on-secondary-fixed': '#00201d', 'secondary': '#3e6560', 'on-surface': '#191d19', 'on-surface-variant': '#404a34', 'surface-container-low': '#f2f5ef', 'surface-container-lowest': '#ffffff', 'surface-container': '#ecefe9', 'surface-container-high': '#e6e9e3', 'surface-container-highest': '#e0e3de', 'on-secondary-fixed-variant': '#264d48', 'error-container': '#ffdad6', 'on-error-container': '#93000a', 'secondary-container': '#bee8e1', 'on-secondary-container': '#426a64' }, borderRadius: { 'DEFAULT': '1rem', 'lg': '2rem', 'xl': '3rem', 'full': '9999px' }, spacing: { 'margin-mobile': '1rem', 'space-sm': '0.5rem', 'space-md': '1rem', 'space-lg': '1.5rem', 'space-xl': '2.5rem', 'margin': '3rem' }, fontFamily: { 'headline-sm': ['Plus Jakarta Sans'], 'headline-xl': ['Plus Jakarta Sans'], 'display-hero': ['Plus Jakarta Sans'], 'body-md': ['DM Sans'], 'label-lg': ['Plus Jakarta Sans'], 'headline-lg': ['Plus Jakarta Sans'], 'body-sm': ['DM Sans'] }, fontSize: { 'headline-sm': ['22px', { lineHeight: '28px', fontWeight: '600' }], 'headline-xl': ['44px', { lineHeight: '50px', letterSpacing: '-0.02em', fontWeight: '700' }], 'display-hero': ['60px', { lineHeight: '64px', letterSpacing: '-0.03em', fontWeight: '800' }], 'body-md': ['15px', { lineHeight: '24px', fontWeight: '400' }], 'label-lg': ['14px', { lineHeight: '20px', fontWeight: '600' }], 'headline-lg': ['28px', { lineHeight: '34px', fontWeight: '700' }], 'body-sm': ['13px', { lineHeight: '18px', fontWeight: '400' }] } } } };</script>
</head>
<body class="bg-surface-container-low font-body-md text-body-md text-on-surface antialiased">

<header class="w-full bg-surface-container-low">
<div class="max-w-5xl mx-auto px-margin-mobile lg:px-margin py-space-lg flex items-center justify-between">
<a href="{{ route('home') }}">@include('partials.logo', ['size' => 26])</a>
<a class="inline-flex items-center justify-center px-space-lg py-space-sm rounded-full bg-primary-container text-on-secondary-fixed font-label-lg text-label-lg font-semibold" href="{{ route('signup.index') }}">Get Started</a>
</div>
</header>

<main class="max-w-3xl mx-auto px-margin-mobile lg:px-margin pb-24">

<div class="text-center pt-space-lg pb-space-xl">
<span class="font-label-lg text-label-lg font-bold text-secondary uppercase tracking-wider mb-space-sm block">Comparison</span>
<h1 class="font-display-hero text-4xl sm:text-display-hero text-on-secondary-fixed font-extrabold tracking-tight leading-tight">Looking for a Foodics alternative?</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-space-md max-w-xl mx-auto">If you run a multi-branch retail shop and want a POS you can actually sign up for online — without a sales call — Tillora might be a better fit.</p>
</div>

<div class="bg-surface-container-lowest rounded-3xl p-space-lg lg:p-space-xl shadow-sm mb-space-xl">
<p class="font-body-md text-body-md text-on-surface-variant">
Foodics is a well-known, enterprise-focused POS platform across the region, built for restaurants and larger retail operations. It's a solid product for teams that want a dedicated account manager and a custom-fit rollout.
Tillora takes a different approach: self-serve signup, transparent pricing, and your own private instance provisioned automatically — built for shops that want to get started today, not next month.
</p>
</div>

<h2 class="font-headline-lg text-headline-lg text-on-secondary-fixed font-bold mb-space-md text-center">How they compare</h2>

<div class="overflow-x-auto mb-space-xl">
<table class="w-full bg-surface-container-lowest rounded-2xl overflow-hidden text-left border-collapse">
<thead>
<tr class="bg-surface-container-high">
<th class="p-space-sm font-label-lg text-label-lg text-on-surface">Feature</th>
<th class="p-space-sm font-label-lg text-label-lg text-on-secondary-fixed bg-primary-container/30">Tillora</th>
<th class="p-space-sm font-label-lg text-label-lg text-on-surface-variant">Typical Enterprise POS</th>
</tr>
</thead>
<tbody class="font-body-sm text-body-sm text-on-surface-variant">
<tr class="border-t border-surface-container-high">
<td class="p-space-sm font-semibold text-on-surface">Sign up online, start today</td>
<td class="p-space-sm bg-primary-container/10"><span class="material-symbols-outlined text-primary text-[18px] align-middle">check</span> Self-serve wizard</td>
<td class="p-space-sm">Usually requires a sales call</td>
</tr>
<tr class="border-t border-surface-container-high">
<td class="p-space-sm font-semibold text-on-surface">Pricing</td>
<td class="p-space-sm bg-primary-container/10"><span class="material-symbols-outlined text-primary text-[18px] align-middle">check</span> Published, transparent</td>
<td class="p-space-sm">Often custom quote only</td>
</tr>
<tr class="border-t border-surface-container-high">
<td class="p-space-sm font-semibold text-on-surface">Your own instance</td>
<td class="p-space-sm bg-primary-container/10"><span class="material-symbols-outlined text-primary text-[18px] align-middle">check</span> Isolated per shop, own domain</td>
<td class="p-space-sm">Varies by vendor</td>
</tr>
<tr class="border-t border-surface-container-high">
<td class="p-space-sm font-semibold text-on-surface">Multi-branch management</td>
<td class="p-space-sm bg-primary-container/10"><span class="material-symbols-outlined text-primary text-[18px] align-middle">check</span> Included</td>
<td class="p-space-sm">Included (often on higher tiers)</td>
</tr>
<tr class="border-t border-surface-container-high">
<td class="p-space-sm font-semibold text-on-surface">Offline-capable checkout</td>
<td class="p-space-sm bg-primary-container/10"><span class="material-symbols-outlined text-primary text-[18px] align-middle">check</span> Included</td>
<td class="p-space-sm">Varies by vendor</td>
</tr>
<tr class="border-t border-surface-container-high">
<td class="p-space-sm font-semibold text-on-surface">Full accounting / GL</td>
<td class="p-space-sm bg-primary-container/10"><span class="material-symbols-outlined text-primary text-[18px] align-middle">check</span> Included</td>
<td class="p-space-sm">Often a separate module</td>
</tr>
<tr class="border-t border-surface-container-high">
<td class="p-space-sm font-semibold text-on-surface">Best for</td>
<td class="p-space-sm bg-primary-container/10">Growing multi-branch shops wanting to move fast</td>
<td class="p-space-sm">Large restaurant chains wanting a dedicated account team</td>
</tr>
</tbody>
</table>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant text-center mb-space-xl">Foodics-specific details above reflect general industry positioning for enterprise POS platforms, not confirmed current pricing or features — check with them directly for exact numbers.</p>

<div class="bg-on-secondary-fixed rounded-3xl p-space-lg lg:p-space-xl text-center">
<h2 class="font-headline-lg text-headline-lg font-extrabold text-surface tracking-tight mb-space-sm">See it for yourself</h2>
<p class="font-body-md text-body-md text-surface-variant mb-space-lg max-w-md mx-auto">Pick a plan, tell us about your shop, and your own Tillora instance is ready in minutes.</p>
<a class="inline-flex items-center justify-center px-space-xl py-4 rounded-full bg-primary-container text-on-secondary-fixed font-label-lg text-label-lg font-bold" href="{{ route('signup.index') }}">Get Started</a>
</div>

</main>

<footer class="text-center py-space-lg font-body-sm text-body-sm text-on-surface-variant">
&copy; {{ date('Y') }} Tillora. <a href="{{ route('home') }}" class="underline">Back to home</a>
</footer>

</body>
</html>
