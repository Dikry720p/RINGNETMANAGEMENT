@extends('layouts.app')

@section('title', 'Pembayaran Bulanan')

@section('content')
    <div class="space-y-6">

        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pembayaran Bulanan</h2>
            <p class="text-sm text-gray-500 mt-1">
                Kelola tagihan pelanggan berdasarkan periode bulanan.
            </p>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-3 rounded-lg">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Filter Periode & Generate --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form action="{{ route('monthly-bills.index') }}" method="GET" class="flex flex-wrap items-end gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Bulan
                    </label>
                    <select name="month" class="rounded-lg border-gray-300 shadow-sm w-44">
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tahun
                    </label>
                    <select name="year" class="rounded-lg border-gray-300 shadow-sm w-32">
                        @foreach (range(now()->year - 2, now()->year + 2) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="px-5 py-2.5 bg-gray-700 text-white rounded-lg hover:bg-gray-800">
                    Tampilkan
                </button>
            </form>

            <div class="border-t border-gray-100 mt-5 pt-5">
                <form action="{{ route('monthly-bills.generate') }}" method="POST"
                    onsubmit="return confirm('Generate tagihan untuk periode ini? Tagihan yang sudah ada tidak akan dibuat ulang.');"
                    class="flex flex-wrap items-center gap-3">
                    @csrf

                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">

                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">
                        Generate Tagihan
                    </button>

                    <span class="text-sm text-gray-500">
                        Periode:
                        {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                        {{ $year }}
                    </span>
                </form>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-sm text-gray-500">Total Tagihan</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-2">
                    {{ number_format($totalBills, 0, ',', '.') }}
                </h3>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-sm text-gray-500">Belum Dibayar</p>
                <h3 class="text-2xl font-bold text-orange-600 mt-2">
                    Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
                </h3>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-sm text-gray-500">Sudah Dibayar</p>
                <h3 class="text-2xl font-bold text-green-600 mt-2">
                    Rp {{ number_format($totalPaid, 0, ',', '.') }}
                </h3>
            </div>

        </div>

        {{-- Tabel Tagihan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="p-5 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">
                    Daftar Tagihan
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Pelanggan
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                No. Telepon
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Paket
                            </th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                Tagihan
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                                Status
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Tanggal Bayar
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">

                        @forelse($bills as $bill)
                            <tr class="hover:bg-gray-50">

                                <td class="px-5 py-4">
                                    <div class="font-medium text-gray-800">
                                        {{ $bill->customer->name ?? '-' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $bill->customer->user_name ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-600">
                                    {{ $bill->customer->phone ?? '-' }}
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-600">
                                    {{ $bill->customer->package->name ?? '-' }}
                                </td>

                                <td class="px-5 py-4 text-right font-semibold text-gray-800 whitespace-nowrap">
                                    Rp {{ number_format((float) $bill->amount, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @if ($bill->status === 'PAID')
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            LUNAS
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                                            BELUM LUNAS
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-600 whitespace-nowrap">
                                    {{ $bill->paid_at ? $bill->paid_at->format('d/m/Y H:i') : '-' }}
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-500">
                                    Belum ada tagihan pada periode ini.
                                    <br>
                                    Gunakan tombol Generate Tagihan untuk membuat tagihan.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            <div class="p-5">
                {{ $bills->links() }}
            </div>

        </div>

    </div>
@endsection
