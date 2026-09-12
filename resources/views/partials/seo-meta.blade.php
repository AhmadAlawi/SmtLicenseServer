@php
    $seoTitle = $title ?? 'Tillora — Retail Cloud POS';
    $seoDescription = $description ?? 'Self-serve multi-branch retail POS. Pick a plan, sign up online, and your own branded instance is live in minutes — no sales call.';
    $seoImage = $image ?? asset('images/og-share.png');
    $seoUrl = $url ?? url()->current();
    $seoType = $type ?? 'website';
    $seoRobots = $robots ?? 'index, follow';
@endphp
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seoRobots }}">
<link rel="canonical" href="{{ $seoUrl }}">

<meta property="og:site_name" content="Tillora">
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:url" content="{{ $seoUrl }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">
