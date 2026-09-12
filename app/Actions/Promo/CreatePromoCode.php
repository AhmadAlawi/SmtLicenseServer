<?php

namespace App\Actions\Promo;

use App\Models\PromoCode;
use Laravel\Cashier\Cashier;

/**
 * Creates the real Stripe Coupon + PromotionCode pair, then mirrors it
 * locally. Two Stripe objects because a Coupon alone has no customer-facing
 * code string — PromotionCode wraps it with the actual code the customer
 * types in, plus its own independent expiry/redemption-limit knobs.
 */
class CreatePromoCode
{
    /** @param  array{code: string, discount_type: string, discount_value: int, duration: string, duration_in_months: ?int, max_redemptions: ?int, expires_at: ?\DateTimeInterface}  $data */
    public function __invoke(array $data, ?int $createdById): PromoCode
    {
        $stripe = Cashier::stripe();

        $couponParams = [
            'duration' => $data['duration'],
            'name'     => $data['code'],
        ];
        if ($data['duration'] === 'repeating') {
            $couponParams['duration_in_months'] = $data['duration_in_months'];
        }
        if ($data['discount_type'] === 'percent') {
            $couponParams['percent_off'] = $data['discount_value'];
        } else {
            $couponParams['amount_off'] = $data['discount_value'];
            $couponParams['currency']   = 'usd';
        }

        $coupon = $stripe->coupons->create($couponParams);

        $promoParams = ['coupon' => $coupon->id, 'code' => $data['code']];
        if (! empty($data['max_redemptions'])) {
            $promoParams['max_redemptions'] = $data['max_redemptions'];
        }
        if (! empty($data['expires_at'])) {
            $promoParams['expires_at'] = $data['expires_at']->getTimestamp();
        }

        $promotionCode = $stripe->promotionCodes->create($promoParams);

        return PromoCode::create([
            'code'                     => $data['code'],
            'stripe_coupon_id'         => $coupon->id,
            'stripe_promotion_code_id' => $promotionCode->id,
            'discount_type'            => $data['discount_type'],
            'discount_value'           => $data['discount_value'],
            'duration'                 => $data['duration'],
            'duration_in_months'       => $data['duration_in_months'] ?? null,
            'max_redemptions'          => $data['max_redemptions'] ?? null,
            'expires_at'               => $data['expires_at'] ?? null,
            'is_active'                => true,
            'created_by'               => $createdById,
        ]);
    }
}
