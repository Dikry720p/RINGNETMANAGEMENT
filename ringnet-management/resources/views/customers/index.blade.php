@extends('layouts.app')

@section('title', 'Data Pelanggan')
@if (session('success'))
    <div class="mb-5 p-4 bg-green-100 text-green-800 rounded-lg">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 p-4 bg-red-100 text-red-800 rounded-lg">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
@section('content')

    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Pelanggan</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola data seluruh pelanggan ISP.
                </p>
            </div>

            <a href="{{ route('customers.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                + Tambah Pelanggan
            </a>
        </div>

        <form action="{{ route('customers.import') }}" method="POST" enctype="multipart/form-data"
            class="flex flex-wrap items-center gap-3">
            @csrf

            <input type="file" name="file" accept=".xlsx,.xls" required
                class="text-sm border border-gray-300 rounded-lg p-2 bg-white">

            <button type="submit"
                class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-semibold">
                Import Excel
            </button>
        </form>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm p-5">

            <form method="GET" action="{{ route('customers.index') }}" class="flex flex-col sm:flex-row gap-3 mb-5">

                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Cari nama, user, atau nomor telepon..."
                    class="w-full sm:max-w-md border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">

                <button type="submit" class="px-5 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">
                    Cari
                </button>

                <a href="{{ route('customers.index') }}"
                    class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-center">
                    Reset
                </a>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama Pelanggan</th>
                            <th class="px-4 py-3 text-left">Nama User</th>
                            <th class="px-4 py-3 text-left">Nomor Telepon</th>
                            <th class="px-4 py-3 text-left">Paket</th>
                            <th class="px-4 py-3 text-left">Tagihan</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ $customer->name }}
                                </td>

                                <td class="px-4 py-3 text-gray-600">
                                    {{ $customer->user_name ?? '-' }}
                                </td>

                                <td class="px-4 py-3 text-gray-600">
                                    {{ $customer->phone ?? '-' }}
                                </td>

                                <td class="px-4 py-3 text-gray-600">
                                    {{ $customer->package->name ?? '-' }}
                                </td>

                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    Rp {{ number_format($customer->billing_amount, 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs
                                    {{ strtoupper($customer->customer_status) === 'AKTIF'
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-gray-100 text-gray-700' }}">
                                        {{ $customer->customer_status }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('customers.edit', $customer) }}"
                                        class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                    Belum ada data pelanggan.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $customers->links() }}
            </div>

        </div>

    </div>

@endsection
