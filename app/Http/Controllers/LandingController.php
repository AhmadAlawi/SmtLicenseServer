<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\View\View;

/** Tillora's marketing site (SaaS conversion plan Phase 8) — the actual front door; /pricing (named signup.*) is the wizard it links into. */
class LandingController extends Controller
{
    public function index(): View
    {
        $plans = Plan::query()->where('is_active', true)->whereNotNull('stripe_price_id')->orderBy('seat_limit')->get();

        return view('landing.index', ['plans' => $plans]);
    }

    /** Comparison/SEO page (marketing-ideas skill, idea #11) — targets "foodics alternative" search intent. */
    public function foodicsAlternative(): View
    {
        $plans = Plan::query()->where('is_active', true)->whereNotNull('stripe_price_id')->orderBy('seat_limit')->get();

        return view('landing.foodics-alternative', ['plans' => $plans]);
    }
}
