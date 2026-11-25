<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Package;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $customers = Customer::with('package')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('user_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        $packages = Package::orderBy('name')->get();

        return view('customers.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'user_name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'activation_date' => 'nullable|date',
            'package_id' => 'nullable|exists:packages,id',
            'billing_amount' => 'required|numeric|min:0',
            'sales' => 'nullable|string',
            'customer_status' => 'required|string|max:30',
        ]);

        Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        $packages = Package::orderBy('name')->get();

        return view('customers.edit', compact('customer', 'packages'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'user_name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'activation_date' => 'nullable|date',
            'package_id' => 'nullable|exists:packages,id',
            'billing_amount' => 'required|numeric|min:0',
            'sales' => 'nullable|string',
            'customer_status' => 'required|string|max:30',
        ]);

        $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }
}
