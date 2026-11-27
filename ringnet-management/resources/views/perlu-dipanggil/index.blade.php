@extends('layouts.app')

@section('title', 'Perlu Dipanggil')

@section('content')
    <div class="space-y-6">

        <div>
            <h2 class="text-2xl font-bold text-gray-800">Perlu Dipanggil</h2>
            <p class="text-sm text-gray-500 mt-1">
                Daftar pelanggan dengan tunggakan 2 bulan berturut-turut atau lebih.
            </p>
        </div>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Pelanggan Perlu Dipanggil</p>
                <h3 class="text-2xl font-bold text-red-600 mt-1">
                    {{ $customers->count() }} Pelanggan
                </h3>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Total Tunggakan</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">
                    Rp {{ number_format($customers->sum('total_arrears'), 0, ',', '.') }}
                </h3>
            </div>

        </div>

        {{-- Tabel Pelanggan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">
                    Daftar Pelanggan
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                No
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Nama Pelanggan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Alamat
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Nomor Telepon
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                                Tunggakan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Periode
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                Total Tunggakan
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">

                        @forelse ($customers as $index => $customer)
                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $index + 1 }}
                                </td>

                                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                    {{ $customer['name'] }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $customer['address'] ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $customer['phone'] ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        {{ $customer['consecutive_unpaid'] }} Bulan
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ implode(', ', $customer['arrears_periods']) }}
                                </td>

                                <td class="px-6 py-4 text-sm font-semibold text-right text-gray-800 whitespace-nowrap">
                                    Rp {{ number_format($customer['total_arrears'], 0, ',', '.') }}
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    Tidak ada pelanggan dengan tunggakan 2 bulan berturut-turut atau lebih.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>

    </div>
@endsection
