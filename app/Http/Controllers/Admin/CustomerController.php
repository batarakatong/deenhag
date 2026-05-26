<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::with('user')
            ->when($request->search, fn ($q) => $q->where(function ($query) use ($request) {
                $query->where('customer_code', 'like', '%'.$request->search.'%')
                    ->orWhere('company_name', 'like', '%'.$request->search.'%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$request->search.'%')->orWhere('email', 'like', '%'.$request->search.'%')->orWhere('phone', 'like', '%'.$request->search.'%'));
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        return view('admin.customers.show', ['customer' => $customer->load(['user', 'orders.items'])]);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => ['required', 'max:120'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'max:30'],
            'company_name' => ['nullable', 'max:160'],
            'address' => ['nullable'],
            'city' => ['nullable', 'max:80'],
            'province' => ['nullable', 'max:80'],
            'postal_code' => ['nullable', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
            'two_factor_enabled' => ['nullable', 'boolean'],
        ]);

        $customer->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'two_factor_enabled' => $request->boolean('two_factor_enabled'),
        ]);
        $customer->update($request->only(['company_name', 'address', 'city', 'province', 'postal_code', 'status']));

        return back()->with('status', 'Customer diperbarui.');
    }

    public function resetPassword(Request $request, Customer $customer)
    {
        $data = $request->validate(['password' => ['required', 'confirmed', 'min:6']]);
        $customer->user->update(['password' => Hash::make($data['password'])]);

        return back()->with('status', 'Password customer berhasil direset.');
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'max:30'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);
        $role = Role::where('name', 'customer')->firstOrFail();
        $user = User::create([
            'role_id' => $role->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);
        $customer = Customer::create(['user_id' => $user->id, 'customer_code' => 'CUST-'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT)]);

        return redirect()->route('admin.customers.show', $customer)->with('status', 'Customer dibuat.');
    }
}
