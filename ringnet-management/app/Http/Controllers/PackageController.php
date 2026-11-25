<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $packages = Package::withCount('customers')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('packages.index', compact('packages', 'search'));
    }

    public function create()
    {
        return view('packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:packages,name',
            'price' => 'required|numeric|min:0',
        ]);

        Package::create($validated);

        return redirect()
            ->route('packages.index')
            ->with('success', 'Jenis paket berhasil ditambahkan.');
    }

    public function edit(Package $package)
    {
        return view('packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:packages,name,' . $package->id,
            'price' => 'required|numeric|min:0',
        ]);

        $package->update($validated);

        return redirect()
            ->route('packages.index')
            ->with('success', 'Jenis paket berhasil diperbarui.');
    }
}
