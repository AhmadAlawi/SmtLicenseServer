<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Pricing — SMT POS SaaS</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f7f7f8; color: #1a1a1a; }
        header { background: #1a1a2e; color: #fff; padding: 24px; text-align: center; }
        main { max-width: 960px; margin: 0 auto; padding: 32px 24px; }
        .plans { display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; }
        .plan { background: #fff; border: 1px solid #e5e5e5; border-radius: 8px; padding: 24px; width: 300px; }
        .plan h2 { margin-top: 0; }
        .plan .seats { color: #555; font-size: 14px; margin-bottom: 16px; }
        .plan ul { padding-left: 18px; font-size: 14px; }
        form.subscribe { margin-top: 16px; }
        label { display: block; font-size: 12px; color: #555; margin-bottom: 2px; margin-top: 8px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        .domain-row { display: flex; align-items: center; gap: 6px; }
        .domain-row input { flex: 1; }
        .domain-suffix { font-size: 13px; color: #555; white-space: nowrap; }
        .slug-status { font-size: 12px; margin-top: 2px; min-height: 16px; }
        .slug-status.ok { color: #0a7a2f; }
        .slug-status.bad { color: #b3261e; }
        button { width: 100%; padding: 10px; margin-top: 16px; background: #1a1a2e; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .error { color: #b3261e; font-size: 13px; margin-bottom: 16px; }
        .empty { text-align: center; color: #666; }
    </style>
</head>
<body>
<header>
    <h1>POS SaaS — Plans</h1>
    <p>Pick a plan, choose your subdomain, and your instance is provisioned automatically.</p>
</header>
<main>
    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    @if ($plans->isEmpty())
        <p class="empty">No plans are configured for online purchase yet.</p>
    @else
        <div class="plans">
            @foreach ($plans as $plan)
                <div class="plan">
                    <h2>{{ $plan->name }}</h2>
                    <div class="seats">{{ $plan->seat_limit ? $plan->seat_limit.' users' : 'Unlimited users' }}</div>
                    <ul>
                        @foreach (collect($plan->features ?? [])->filter() as $key => $enabled)
                            <li>{{ str($key)->replace('_', ' ')->title() }}</li>
                        @endforeach
                    </ul>
                    <form class="subscribe" method="POST" action="{{ route('pricing.signup') }}">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $plan->code }}">

                        <label>Your subdomain</label>
                        <div class="domain-row">
                            <input type="text" name="subdomain" class="subdomain-input" placeholder="yourcompany" required
                                   pattern="[a-z0-9]+(-[a-z0-9]+)*" minlength="3" maxlength="30">
                            <span class="domain-suffix">.{{ $rootDomain ?: 'yourdomain.com' }}</span>
                        </div>
                        <div class="slug-status"></div>

                        <label>Company name</label>
                        <input type="text" name="company_name" required>

                        <label>Your name</label>
                        <input type="text" name="admin_name" required>

                        <label>Email (also your admin login)</label>
                        <input type="email" name="admin_email" required>

                        <label>Admin password</label>
                        <input type="password" name="admin_password" minlength="8" required>

                        <label>Confirm password</label>
                        <input type="password" name="admin_password_confirmation" minlength="8" required>

                        <button type="submit">Subscribe to {{ $plan->name }}</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</main>
<script>
    // Live subdomain availability check — debounced, best-effort UX only;
    // the server re-validates on submit regardless (uniqueness is enforced
    // there, this is just faster feedback).
    let timer;
    document.querySelectorAll('.subdomain-input').forEach((input) => {
        input.addEventListener('input', () => {
            clearTimeout(timer);
            const status = input.closest('form').querySelector('.slug-status');
            const value = input.value.trim();
            if (value.length < 3) { status.textContent = ''; return; }
            timer = setTimeout(async () => {
                try {
                    const res = await fetch(`{{ route('pricing.check-subdomain') }}?subdomain=${encodeURIComponent(value)}`);
                    const data = await res.json();
                    status.className = 'slug-status ' + (data.available ? 'ok' : 'bad');
                    status.textContent = !data.valid
                        ? 'Only lowercase letters, numbers, and hyphens.'
                        : (data.available ? 'Available' : 'Already taken');
                } catch (e) { /* network hiccup — server-side validation still applies on submit */ }
            }, 400);
        });
    });
</script>
</body>
</html>
