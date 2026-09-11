<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set up your shop — Tillora</title>
    <style>
        :root { --ink: #1a1a2e; --teal: #0d9488; --border: #e5e5e5; }
        * { box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; margin: 0; background: #f7f7f8; color: var(--ink); }
        header { padding: 20px 24px; }
        main { max-width: 560px; margin: 0 auto 64px; padding: 0 24px; }
        .card { background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 32px; }

        .progress { display: flex; gap: 6px; margin-bottom: 28px; }
        .progress span { flex: 1; height: 4px; border-radius: 2px; background: var(--border); }
        .progress span.done { background: var(--teal); }

        h2 { margin: 0 0 4px; font-size: 20px; }
        .step-sub { color: #666; font-size: 13px; margin: 0 0 24px; }

        label { display: block; font-size: 13px; color: #444; margin: 16px 0 4px; font-weight: 600; }
        label:first-child { margin-top: 0; }
        input[type=text], input[type=email], input[type=password], input[type=number], input[type=file] {
            width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 14px; box-sizing: border-box;
        }
        .hint { font-size: 12px; color: #888; margin-top: 4px; }

        .plans { display: grid; gap: 12px; }
        .plan-card { border: 2px solid var(--border); border-radius: 8px; padding: 16px; cursor: pointer; }
        .plan-card.selected { border-color: var(--ink); background: #fafafa; }
        .plan-card h3 { margin: 0 0 4px; font-size: 15px; }
        .plan-card .seats { font-size: 13px; color: #666; }
        .plan-card input { display: none; }

        .domain-row { display: flex; align-items: center; gap: 6px; }
        .domain-row input { flex: 1; }
        .domain-suffix { font-size: 13px; color: #666; white-space: nowrap; }
        .slug-status { font-size: 12px; margin-top: 4px; min-height: 16px; }
        .slug-status.ok { color: #0a7a2f; }
        .slug-status.bad { color: #b3261e; }

        .review dt { font-size: 12px; color: #888; margin-top: 12px; }
        .review dd { margin: 2px 0 0; font-size: 14px; }

        .actions { display: flex; justify-content: space-between; margin-top: 28px; }
        button { padding: 11px 24px; border-radius: 6px; font-weight: 600; font-size: 14px; cursor: pointer; border: none; }
        .btn-next { background: var(--ink); color: #fff; margin-left: auto; }
        .btn-back { background: transparent; color: #666; }
        .btn-submit { background: var(--teal); color: #fff; margin-left: auto; }
        .error { color: #b3261e; font-size: 13px; margin-bottom: 16px; }
    </style>
</head>
<body>
<header>@include('partials.logo')</header>
<main>
    <div class="card">
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <div class="progress">
            <span class="s-ind done" data-for="1"></span>
            <span class="s-ind" data-for="2"></span>
            <span class="s-ind" data-for="3"></span>
            <span class="s-ind" data-for="4"></span>
            <span class="s-ind" data-for="5"></span>
        </div>

        <form method="POST" action="{{ route('signup.store') }}" enctype="multipart/form-data" id="wizard">
            @csrf

            <section data-step="1">
                <h2>Choose your plan</h2>
                <p class="step-sub">You can change this later.</p>
                <div class="plans">
                    @foreach ($plans as $plan)
                        <label class="plan-card" data-plan-card>
                            <input type="radio" name="plan" value="{{ $plan->code }}" {{ old('plan', request('plan')) === $plan->code ? 'checked' : '' }} required>
                            <h3>{{ $plan->name }}</h3>
                            <div class="seats">{{ $plan->seat_limit ? $plan->seat_limit.' users' : 'Unlimited users' }}</div>
                        </label>
                    @endforeach
                </div>
            </section>

            <section data-step="2" hidden>
                <h2>Tell us about your shop</h2>
                <p class="step-sub">This becomes the name on your receipts and login screen.</p>

                <label for="company_name">Shop name</label>
                <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" required>

                <label for="logo">Logo (optional)</label>
                <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                <div class="hint">PNG, JPG, SVG, or WebP. You can add or change this later too.</div>

                <label for="branch_count">How many branches do you have?</label>
                <input type="number" id="branch_count" name="branch_count" min="1" max="500" value="{{ old('branch_count', 1) }}">
                <div class="hint">Just for sizing your plan — you'll set each branch up inside your admin panel.</div>
            </section>

            <section data-step="3" hidden>
                <h2>Pick your subdomain</h2>
                <p class="step-sub">This is where your shop will live.</p>
                <label for="subdomain">Subdomain</label>
                <div class="domain-row">
                    <input type="text" id="subdomain" name="subdomain" class="subdomain-input" placeholder="yourshop"
                           value="{{ old('subdomain') }}" pattern="[a-z0-9]+(-[a-z0-9]+)*" minlength="3" maxlength="30" required>
                    <span class="domain-suffix">.{{ $rootDomain ?: 'tillora.app' }}</span>
                </div>
                <div class="slug-status"></div>
            </section>

            <section data-step="4" hidden>
                <h2>Create your admin account</h2>
                <p class="step-sub">You'll use this to log into your shop.</p>

                <label for="admin_name">Your name</label>
                <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required>

                <label for="admin_email">Email (also your login)</label>
                <input type="email" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required>

                <label for="admin_password">Password</label>
                <input type="password" id="admin_password" name="admin_password" minlength="8" required>

                <label for="admin_password_confirmation">Confirm password</label>
                <input type="password" id="admin_password_confirmation" name="admin_password_confirmation" minlength="8" required>
            </section>

            <section data-step="5" hidden>
                <h2>Review &amp; confirm</h2>
                <p class="step-sub">We'll email you a confirmation link before payment.</p>
                <dl class="review" id="review"></dl>
            </section>

            <div class="actions">
                <button type="button" class="btn-back" data-back hidden>Back</button>
                <button type="button" class="btn-next" data-next>Continue</button>
                <button type="submit" class="btn-submit" data-submit hidden>Confirm &amp; continue to payment</button>
            </div>
        </form>
    </div>
</main>
<script>
    const form = document.getElementById('wizard');
    const steps = [...form.querySelectorAll('section[data-step]')];
    const indicators = [...document.querySelectorAll('.s-ind')];
    let current = 1;

    function showStep(n) {
        steps.forEach(s => s.hidden = Number(s.dataset.step) !== n);
        indicators.forEach(i => i.classList.toggle('done', Number(i.dataset.for) <= n));
        document.querySelector('[data-back]').hidden = n === 1;
        document.querySelector('[data-next]').hidden = n === steps.length;
        document.querySelector('[data-submit]').hidden = n !== steps.length;
        if (n === steps.length) renderReview();
        current = n;
    }

    function currentStepValid() {
        const section = steps.find(s => Number(s.dataset.step) === current);
        return [...section.querySelectorAll('input')].every(i => i.checkValidity());
    }

    document.querySelectorAll('[data-plan-card]').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-plan-card]').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            card.querySelector('input').checked = true;
        });
        if (card.querySelector('input').checked) card.classList.add('selected');
    });

    document.querySelector('[data-next]').addEventListener('click', () => {
        if (!currentStepValid()) { form.reportValidity(); return; }
        if (current < steps.length) showStep(current + 1);
    });
    document.querySelector('[data-back]').addEventListener('click', () => {
        if (current > 1) showStep(current - 1);
    });

    function renderReview() {
        const val = (name) => form.querySelector(`[name="${name}"]`)?.value || '—';
        const planCard = document.querySelector('[data-plan-card] input:checked')?.closest('[data-plan-card]');
        const planName = planCard ? planCard.querySelector('h3').textContent : '—';
        const logoFile = form.querySelector('#logo').files[0];
        document.getElementById('review').innerHTML = `
            <dt>Plan</dt><dd>${planName}</dd>
            <dt>Shop name</dt><dd>${val('company_name')}</dd>
            <dt>Logo</dt><dd>${logoFile ? logoFile.name : 'None uploaded'}</dd>
            <dt>Branches</dt><dd>${val('branch_count')}</dd>
            <dt>Subdomain</dt><dd>${val('subdomain')}.{{ $rootDomain ?: 'tillora.app' }}</dd>
            <dt>Admin</dt><dd>${val('admin_name')} — ${val('admin_email')}</dd>
        `;
    }

    // Live subdomain availability check — debounced, best-effort UX only;
    // the server re-validates on submit regardless (uniqueness is enforced
    // there, this is just faster feedback).
    let timer;
    const subdomainInput = document.querySelector('.subdomain-input');
    subdomainInput.addEventListener('input', () => {
        clearTimeout(timer);
        const status = document.querySelector('.slug-status');
        const value = subdomainInput.value.trim();
        if (value.length < 3) { status.textContent = ''; return; }
        timer = setTimeout(async () => {
            try {
                const res = await fetch(`{{ route('signup.check-subdomain') }}?subdomain=${encodeURIComponent(value)}`);
                const data = await res.json();
                status.className = 'slug-status ' + (data.available ? 'ok' : 'bad');
                status.textContent = !data.valid
                    ? 'Only lowercase letters, numbers, and hyphens.'
                    : (data.available ? 'Available' : 'Already taken');
            } catch (e) { /* network hiccup — server-side validation still applies on submit */ }
        }, 400);
    });

    showStep(1);
</script>
</body>
</html>
