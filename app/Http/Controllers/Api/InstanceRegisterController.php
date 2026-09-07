<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateLicenseForCustomer;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * POST /api/v1/instances/register — called once by the provisioning
 * pipeline (never by an instance) when a new customer's container is about
 * to be built. Mints the license_key + hmac_secret pair the pipeline then
 * templates into that instance's .env, via the same
 * {@see CreateLicenseForCustomer} action the Stripe Checkout webhook uses
 * for the auto-provisioned path — this endpoint exists for the manual /
 * ops-triggered case (re-provisioning, a second branch instance for an
 * existing customer, staging).
 */
class InstanceRegisterController extends Controller
{
    public function __invoke(Request $request, CreateLicenseForCustomer $createLicense): JsonResponse
    {
        $data = $request->validate([
            'customer_email' => ['required', 'email'],
            'customer_name'  => ['required', 'string', 'max:255'],
            'plan_code'      => ['required', Rule::exists('plans', 'code')],
            'domain_url'     => ['nullable', 'string', 'max:255'],
        ]);

        $customer = Customer::query()->firstOrCreate(
            ['email' => $data['customer_email']],
            ['name' => $data['customer_name']],
        );

        $plan = Plan::query()->where('code', $data['plan_code'])->firstOrFail();

        $result = $createLicense($customer, $plan, $data['domain_url'] ?? null);

        return response()->json([
            'license_id'  => $result['license']->id,
            'license_key' => $result['license_key'],
            'hmac_secret' => $result['hmac_secret'],
        ], 201);
    }
}
