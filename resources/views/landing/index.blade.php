<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tillora — Point of Sale for Growing Retail Chains</title>
    <style>
        :root { --ink: #1a1a2e; --teal: #0d9488; --bg: #f7f7f8; --border: #e5e5e5; }
        * { box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; margin: 0; color: var(--ink); background: #fff; line-height: 1.5; }
        a { color: inherit; }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 0 24px; }

        header.nav { border-bottom: 1px solid var(--border); position: sticky; top: 0; background: #fff; z-index: 10; }
        header.nav .wrap { display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; }
        .nav-links { display: flex; gap: 28px; align-items: center; font-size: 14px; }
        .nav-links a.cta { background: var(--ink); color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none; }
        .nav-links a:not(.cta) { text-decoration: none; color: #444; }

        .hero { background: linear-gradient(180deg, #f7f7f8 0%, #fff 100%); padding: 80px 24px 64px; text-align: center; }
        .hero h1 { font-size: 44px; margin: 0 0 16px; letter-spacing: -0.02em; }
        .hero p { font-size: 18px; color: #555; max-width: 640px; margin: 0 auto 32px; }
        .hero .actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn-primary { background: var(--ink); color: #fff; padding: 14px 28px; border-radius: 6px; text-decoration: none; font-weight: 600; }
        .btn-secondary { background: #fff; border: 1px solid var(--border); color: var(--ink); padding: 14px 28px; border-radius: 6px; text-decoration: none; font-weight: 600; }

        section { padding: 64px 24px; }
        section h2 { font-size: 30px; text-align: center; margin: 0 0 12px; }
        section .lead { text-align: center; color: #666; max-width: 560px; margin: 0 auto 40px; }

        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; }
        .feature { border: 1px solid var(--border); border-radius: 10px; padding: 24px; }
        .feature .icon { width: 40px; height: 40px; border-radius: 8px; background: var(--teal); margin-bottom: 16px; }
        .feature h3 { margin: 0 0 8px; font-size: 17px; }
        .feature p { margin: 0; color: #666; font-size: 14px; }

        .steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; }
        .step { text-align: center; }
        .step .num { width: 36px; height: 36px; border-radius: 50%; background: var(--ink); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; margin: 0 auto 12px; }
        .step h3 { font-size: 16px; margin: 0 0 6px; }
        .step p { color: #666; font-size: 14px; margin: 0; }

        .social-proof { background: var(--bg); text-align: center; }
        .social-proof .note { font-size: 12px; color: #999; margin-top: 8px; }
        .logos { display: flex; gap: 32px; justify-content: center; flex-wrap: wrap; opacity: 0.5; font-weight: 600; color: #999; margin-top: 16px; }

        .pricing-grid { display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; }
        .plan { background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 28px; width: 280px; }
        .plan h3 { margin: 0 0 4px; }
        .plan .seats { color: #666; font-size: 14px; margin-bottom: 16px; }
        .plan ul { padding-left: 18px; font-size: 14px; color: #444; margin: 0 0 20px; }
        .plan a.subscribe { display: block; text-align: center; background: var(--ink); color: #fff; padding: 10px; border-radius: 6px; text-decoration: none; font-weight: 600; }
        .empty { text-align: center; color: #666; }

        .faq details { border-bottom: 1px solid var(--border); padding: 16px 0; max-width: 720px; margin: 0 auto; }
        .faq summary { cursor: pointer; font-weight: 600; font-size: 15px; }
        .faq p { color: #666; margin: 10px 0 0; font-size: 14px; }

        footer { border-top: 1px solid var(--border); padding: 32px 24px; text-align: center; color: #999; font-size: 13px; }
        footer a { text-decoration: none; color: #666; margin: 0 8px; }
    </style>
</head>
<body>

<header class="nav">
    <div class="wrap">
        @include('partials.logo')
        <nav class="nav-links">
            <a href="#features">Features</a>
            <a href="#pricing">Pricing</a>
            <a href="#faq">FAQ</a>
            <a href="{{ route('login') }}">Staff login</a>
            <a class="cta" href="{{ route('signup.index') }}">Start now</a>
        </nav>
    </div>
</header>

<section class="hero">
    <h1>Point of sale that scales<br>with every branch you open</h1>
    <p>Tillora runs your checkout, inventory, and reporting across every store you own — from one login. Sign up, pick your subdomain, and your own instance is ready in minutes.</p>
    <div class="actions">
        <a class="btn-primary" href="{{ route('signup.index') }}">Start your free setup</a>
        <a class="btn-secondary" href="#pricing">See pricing</a>
    </div>
</section>

<section id="features">
    <h2>Everything a growing retail chain needs</h2>
    <p class="lead">One system, every branch — not a spreadsheet stitched to five different tools.</p>
    <div class="features">
        <div class="feature"><div class="icon"></div><h3>Multi-branch management</h3><p>Run checkout, stock, and staff across every location from a single admin panel.</p></div>
        <div class="feature"><div class="icon"></div><h3>Real-time inventory</h3><p>Stock levels update the moment a sale happens — no end-of-day reconciliation.</p></div>
        <div class="feature"><div class="icon"></div><h3>Receipts &amp; barcode printing</h3><p>Thermal receipts, product barcodes, and shelf labels, ready out of the box.</p></div>
        <div class="feature"><div class="icon"></div><h3>Works offline</h3><p>The cashier screen keeps ringing up sales even if the internet drops, and syncs when it's back.</p></div>
        <div class="feature"><div class="icon"></div><h3>Full accounting</h3><p>Chart of accounts, journal entries, and financial reports built in — not bolted on.</p></div>
        <div class="feature"><div class="icon"></div><h3>Your own domain</h3><p>Your shop gets its own private instance and web address, not a shared multi-tenant app.</p></div>
    </div>
</section>

<section class="social-proof">
    <h2>Trusted by retail teams</h2>
    <div class="logos">
        <span>Shop A</span><span>Retail Co.</span><span>Branch Group</span><span>Storefront Inc.</span>
    </div>
    <p class="note">Placeholder — real customer logos go here once available.</p>
</section>

<section>
    <h2>Live in minutes, not weeks</h2>
    <div class="steps">
        <div class="step"><div class="num">1</div><h3>Pick a plan</h3><p>Choose the tier that fits how many branches you run.</p></div>
        <div class="step"><div class="num">2</div><h3>Tell us about your shop</h3><p>Name, logo, branch count, and your subdomain.</p></div>
        <div class="step"><div class="num">3</div><h3>Start selling</h3><p>Your own Tillora instance is provisioned automatically — log in and go.</p></div>
    </div>
</section>

<section id="pricing">
    <h2>Simple, per-branch pricing</h2>
    <p class="lead">Every plan includes the full platform — the difference is how many people and branches you need.</p>
    @if ($plans->isEmpty())
        <p class="empty">No plans are configured for online purchase yet.</p>
    @else
        <div class="pricing-grid">
            @foreach ($plans as $plan)
                <div class="plan">
                    <h3>{{ $plan->name }}</h3>
                    <div class="seats">{{ $plan->seat_limit ? $plan->seat_limit.' users' : 'Unlimited users' }}</div>
                    <ul>
                        @foreach (collect($plan->features ?? [])->filter() as $key => $enabled)
                            <li>{{ str($key)->replace('_', ' ')->title() }}</li>
                        @endforeach
                    </ul>
                    <a class="subscribe" href="{{ route('signup.index', ['plan' => $plan->code]) }}">Choose {{ $plan->name }}</a>
                </div>
            @endforeach
        </div>
    @endif
</section>

<section id="faq" class="faq">
    <h2>Frequently asked questions</h2>
    <details>
        <summary>Do I get my own domain?</summary>
        <p>Yes — every shop gets its own subdomain automatically, and you can point your own domain at it later.</p>
    </details>
    <details>
        <summary>Can I change plans later?</summary>
        <p>Yes, reach out to support and we'll move you to a different tier — your data and setup stay exactly as they are.</p>
    </details>
    <details>
        <summary>Does it work without internet?</summary>
        <p>The checkout screen keeps working offline and syncs automatically once you're back online.</p>
    </details>
    <details>
        <summary>How many branches can I add?</summary>
        <p>As many as your plan's user limit supports — branches themselves aren't capped, you manage them from your admin panel after setup.</p>
    </details>
</section>

<footer>
    <div>&copy; {{ date('Y') }} Tillora. All rights reserved.</div>
    <div><a href="{{ route('signup.index') }}">Get started</a> · <a href="{{ route('login') }}">Staff login</a></div>
</footer>

</body>
</html>
