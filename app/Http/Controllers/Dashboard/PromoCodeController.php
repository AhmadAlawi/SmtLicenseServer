<?php

namespace App\Http\Controllers\Dashboard;

use App\Actions\Promo\CreatePromoCode;
use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Laravel\Cashier\Cashier;

class PromoCodeController extends Controller
{
    public function index(): View
    {
        $promoCodes = PromoCode::query()->with('creator')->latest()->paginate(25);

        return view('dashboard.promo-codes.index', compact('promoCodes'));
    }

    public function create(): View
    {
        return view('dashboard.promo-codes.create');
    }

    public function store(Request $request, CreatePromoCode $create): RedirectResponse
    {
        $data = $request->validate([
            'code'                => ['required', 'string', 'max:32', 'alpha_dash', Rule::unique('promo_codes', 'code')],
            'discount_type'       => ['required', 'in:percent,amount'],
            'discount_value'      => ['required', 'integer', 'min:1'],
            'duration'            => ['required', 'in:once,repeating,forever'],
            'duration_in_months'  => ['required_if:duration,repeating', 'nullable', 'integer', 'in:1,12'],
            'max_redemptions'     => ['nullable', 'integer', 'min:1'],
            'expires_at'          => ['nullable', 'date', 'after:now'],
        ]);

        if ($data['discount_type'] === 'percent' && $data['discount_value'] > 100) {
            return back()->withErrors(['discount_value' => 'Percent-off can\'t exceed 100.'])->withInput();
        }

        $data['code'] = strtoupper($data['code']);
        $data['expires_at'] = $data['expires_at'] ?? null;

        try {
            $promoCode = $create($data, $request->user()->id);
        } catch (\Throwable $e) {
            return back()->withErrors(['code' => "Stripe rejected this: {$e->getMessage()}"])->withInput();
        }

        return redirect()->route('dashboard.promo-codes.index')->with('status', "Promo code \"{$promoCode->code}\" created.");
    }

    /** Deactivates on both sides — Stripe coupons/promotion codes can't be deleted, only archived. */
    public function deactivate(PromoCode $promoCode): RedirectResponse
    {
        // Same API-version pin as CreatePromoCode — this account's default
        // version behaves oddly on this resource.
        Cashier::stripe()->promotionCodes->update($promoCode->stripe_promotion_code_id, ['active' => false], ['stripe_version' => '2024-06-20']);
        $promoCode->update(['is_active' => false]);

        return back()->with('status', "Promo code \"{$promoCode->code}\" deactivated.");
    }
}
