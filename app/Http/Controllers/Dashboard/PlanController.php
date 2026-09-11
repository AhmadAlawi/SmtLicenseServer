<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Plan management (SaaS conversion plan Phase 8) — plans used to exist
 * only via {@see \Database\Seeders\PlansSeeder}; staff need to create/edit
 * them for real. Never hard-delete a plan — `licenses.plan_id` references
 * it — `toggleActive()` (is_active) is the removal path instead, same as
 * how `stripe_price_id` being null already keeps a plan off the public
 * pricing/signup pages.
 */
class PlanController extends Controller
{
    /** Feature flags actually gated somewhere in the SaasPOS app — keep this list in sync with what feature_enabled() checks, don't invent flags nothing reads. */
    private const FEATURE_KEYS = ['multi_store', 'advanced_reporting'];

    public function index(): View
    {
        $plans = Plan::query()->orderBy('seat_limit')->get();

        return view('dashboard.plans.index', compact('plans'));
    }

    public function create(): View
    {
        return view('dashboard.plans.create', ['featureKeys' => self::FEATURE_KEYS]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Plan::query()->create($data);

        return redirect()->route('dashboard.plans.index')->with('status', "Plan \"{$data['name']}\" created.");
    }

    public function edit(Plan $plan): View
    {
        return view('dashboard.plans.edit', ['plan' => $plan, 'featureKeys' => self::FEATURE_KEYS]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $data = $this->validated($request, $plan);

        $plan->update($data);

        return redirect()->route('dashboard.plans.index')->with('status', "Plan \"{$plan->name}\" updated.");
    }

    public function toggleActive(Plan $plan): RedirectResponse
    {
        $plan->update(['is_active' => ! $plan->is_active]);

        $state = $plan->is_active ? 'activated' : 'deactivated';

        return back()->with('status', "Plan \"{$plan->name}\" {$state}.");
    }

    /** @return array<string,mixed> */
    private function validated(Request $request, ?Plan $plan = null): array
    {
        $data = $request->validate([
            'code'            => ['required', 'string', 'max:32', 'alpha_dash', Rule::unique('plans', 'code')->ignore($plan)],
            'name'            => ['required', 'string', 'max:255'],
            'display_price'   => ['nullable', 'string', 'max:32'],
            'seat_limit'      => ['nullable', 'integer', 'min:1'],
            'stripe_price_id' => ['nullable', 'string', 'max:255'],
            'features'        => ['nullable', 'array'],
            'features.*'      => ['string', Rule::in(self::FEATURE_KEYS)],
        ]);

        $data['features'] = array_fill_keys($data['features'] ?? [], true);
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
