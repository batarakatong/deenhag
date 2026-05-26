<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerProfileController extends Controller
{
    public function edit()
    {
        $customer = Customer::firstOrCreate(
            ['user_id' => auth()->id()],
            ['customer_code' => 'CUST-'.str_pad((string) auth()->id(), 5, '0', STR_PAD_LEFT)]
        );

        return view('customer.profile', compact('customer'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:80'],
            'province' => ['nullable', 'string', 'max:80'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        auth()->user()->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
        ]);

        Customer::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'customer_code' => 'CUST-'.str_pad((string) auth()->id(), 5, '0', STR_PAD_LEFT),
                'company_name' => $data['company_name'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'province' => $data['province'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
            ]
        );

        return back()->with('status', 'Profil akun diperbarui.');
    }
}
