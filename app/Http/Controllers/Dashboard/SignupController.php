<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PendingSignup;
use Illuminate\View\View;

/** Read-only view of every signup attempt (converted or not) with its campaign attribution — SaaS conversion plan analytics gap fix. */
class SignupController extends Controller
{
    public function index(): View
    {
        $signups = PendingSignup::query()->latest()->paginate(25);

        $convertedEmails = Customer::query()
            ->whereIn('email', $signups->pluck('admin_email'))
            ->pluck('email')
            ->flip();

        return view('dashboard.signups.index', compact('signups', 'convertedEmails'));
    }
}
