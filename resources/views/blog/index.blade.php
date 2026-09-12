<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
@include('partials.seo-meta', [
    'title' => 'Blog — Tillora',
    'description' => 'Guides on multi-branch retail management, POS software, and running a retail shop in Jordan and the MENA region.',
])
<link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&amp;family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&amp;display=swap" rel="stylesheet"/>
<style>@layer base{html,body{margin:0;padding:0;}}::-webkit-scrollbar{display:none;}</style>
<script src="https://cdn.tailwindcss.com"></script>
<script id="tailwind-config">tailwind.config = { theme: { extend: { colors: { 'primary-container': '#9cff1e', 'on-secondary-fixed': '#00201d', 'secondary': '#3e6560', 'on-surface': '#191d19', 'on-surface-variant': '#404a34', 'surface-container-low': '#f2f5ef', 'surface-container-lowest': '#ffffff' }, fontFamily: { 'headline-xl': ['Plus Jakarta Sans'], 'headline-lg': ['Plus Jakarta Sans'], 'body-md': ['DM Sans'], 'body-sm': ['DM Sans'] } } } };</script>
@include('partials.analytics')
</head>
<body class="bg-surface-container-low font-body-md text-on-surface antialiased">

<header class="w-full bg-surface-container-low">
<div class="max-w-5xl mx-auto px-4 lg:px-8 py-6 flex items-center justify-between">
<a href="{{ route('home') }}">@include('partials.logo', ['size' => 26])</a>
<a class="inline-flex items-center justify-center px-6 py-2 rounded-full bg-primary-container text-on-secondary-fixed font-bold" href="{{ route('signup.index') }}">Get Started</a>
</div>
</header>

<main class="max-w-3xl mx-auto px-4 lg:px-8 pb-24">
<h1 class="font-headline-xl text-4xl font-extrabold text-on-secondary-fixed tracking-tight mb-8 mt-4">Tillora Blog</h1>

<div class="flex flex-col gap-6">
@foreach ($posts as $post)
<a href="{{ route('blog.show', $post) }}" class="block bg-surface-container-lowest rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
    <h2 class="font-headline-lg text-xl font-bold text-on-secondary-fixed mb-2">{{ $post->title }}</h2>
    <p class="text-on-surface-variant text-sm">{{ $post->excerpt }}</p>
</a>
@endforeach
</div>

<div class="mt-10">
{{ $posts->links() }}
</div>
</main>

<footer class="text-center py-6 text-sm text-on-surface-variant">
&copy; {{ date('Y') }} Tillora. <a href="{{ route('home') }}" class="underline">Back to home</a>
</footer>

</body>
</html>
