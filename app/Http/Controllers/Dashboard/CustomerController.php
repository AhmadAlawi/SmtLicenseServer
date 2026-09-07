<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::query()->withCount('licenses')->orderBy('name')->paginate(25);

        return view('dashboard.customers.index', compact('customers'));
    }

    public function show(Customer $customer): View
    {
        $customer->load(['licenses.plan', 'licenses.instance']);

        return view('dashboard.customers.show', compact('customer'));
    }
}
