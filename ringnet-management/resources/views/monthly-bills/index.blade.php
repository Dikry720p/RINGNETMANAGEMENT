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

            <div class="flex flex-wrap items-center justify-between gap-4 mb-5">

                {{-- Navigasi Periode --}}
                @php
                    $currentPeriod = \Carbon\Carbon::createFromDate($year, $month, 1);
                    $previousPeriod = $currentPeriod->copy()->subMonth();
                    $nextPeriod = $currentPeriod->copy()->addMonth();
                @endphp

                <div class="flex items-center gap-3">
                    <a href="{{ route('monthly-bills.index', ['month' => $previousPeriod->month, 'year' => $previousPeriod->year]) }}"
                        class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 hover:bg-gray-100 transition"
                        title="Bulan sebelumnya">
                        &larr;
                    </a>

                    <div class="text-center min-w-[150px]">
                        <p class="text-xs text-gray-500">Periode Tagihan</p>
                        <h3 class="text-lg font-bold text-gray-800">
                            {{ $currentPeriod->translatedFormat('F Y') }}
                        </h3>
                    </div>

                    <a href="{{ route('monthly-bills.index', ['month' => $nextPeriod->month, 'year' => $nextPeriod->year]) }}"
                        class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 hover:bg-gray-100 transition"
                        title="Bulan berikutnya">
                        &rarr;
                    </a>
                </div>

                {{-- Pemilihan Bulan & Tahun --}}
                <form action="{{ route('monthly-bills.index') }}" method="GET" class="flex flex-wrap items-end gap-3">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Bulan
                        </label>
                        <select name="month" class="rounded-lg border-gray-300 shadow-sm w-40">
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
                        <select name="year" class="rounded-lg border-gray-300 shadow-sm w-28">
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

            </div>

            {{-- Generate Tagihan --}}
            <div class="border-t border-gray-100 pt-5">

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
                        Periode: {{ $currentPeriod->translatedFormat('F Y') }} (pelanggan baru masuk mulai periode
                        berikutnya)
                    </span>

                </form>

            </div>

        </div>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-sm text-gray-500">Total Tagihan Periode</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-2">
                    {{ number_format($totalBills, 0, ',', '.') }} tagihan
                </h3>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-sm text-gray-500">Belum Dibayar Periode</p>
                <h3 class="text-2xl font-bold text-orange-600 mt-2">
                    Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
                </h3>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-sm text-gray-500">Tagihan Lunas Periode</p>
                <h3 class="text-2xl font-bold text-green-600 mt-2">
                    Rp {{ number_format($totalPaid, 0, ',', '.') }}
                </h3>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-sm text-gray-500">Pembayaran Diterima Bulan Ini</p>
                <h3 class="text-2xl font-bold text-indigo-600 mt-2">
                    Rp {{ number_format($paymentsReceived, 0, ',', '.') }}
                </h3>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-sm text-gray-500">Total Tunggakan</p>
                <h3 class="text-2xl font-bold text-red-600 mt-2">
                    Rp {{ number_format($totalArrears, 0, ',', '.') }}
                </h3>
            </div>

        </div>

        {{-- Tabel Tagihan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="p-5 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <h3 class="font-semibold text-gray-800">
                    Daftar Tagihan
                </h3>

                <form id="billSearchForm" action="{{ route('monthly-bills.index') }}" method="GET"
                    class="flex flex-wrap items-center gap-2">

                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">

                    <input type="text" id="billSearchInput" name="search" value="{{ $search }}"
                        placeholder="Cari nama, no. tlp, username, paket..." autocomplete="off"
                        class="w-full md:w-72 rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <select id="billStatusFilter" name="status"
                        class="rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">

                        <option value="" {{ $status === '' ? 'selected' : '' }}>
                            Semua Status
                        </option>

                        <option value="UNPAID" {{ $status === 'UNPAID' ? 'selected' : '' }}>
                            Belum Lunas
                        </option>

                        <option value="PAID" {{ $status === 'PAID' ? 'selected' : '' }}>
                            Lunas
                        </option>

                    </select>

                    <button type="submit"
                        class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 text-sm font-medium">
                        Filter
                    </button>

                    <a id="billResetButton" href="{{ route('monthly-bills.index', ['month' => $month, 'year' => $year]) }}"
                        class="{{ $search !== '' || $status !== '' ? 'inline-flex' : 'hidden' }} px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
                        Reset
                    </a>

                </form>

            </div>

            {{-- Hasil Pencarian / Tabel --}}
            <div id="billResults">
                @include('monthly-bills._ajax-results')
            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('billSearchForm');
            const input = document.getElementById('billSearchInput');
            const status = document.getElementById('billStatusFilter');
            const resetBtn = document.getElementById('billResetButton');
            const results = document.getElementById('billResults');

            if (!form || !input || !status || !results) return;

            let controller = null;

            function toggleResetButton() {
                if (!resetBtn) return;

                const hasFilter = input.value.trim() !== '' || status.value !== '';
                resetBtn.classList.toggle('hidden', !hasFilter);
                resetBtn.classList.toggle('inline-flex', hasFilter);
            }

            function getSearchUrl() {
                const params = new URLSearchParams(new FormData(form));
                return form.action + '?' + params.toString();
            }

            function loadResults(url) {
                if (controller) controller.abort();

                controller = new AbortController();
                results.style.opacity = '0.5';

                if (window.history && window.history.replaceState) {
                    window.history.replaceState({}, '', url);
                }

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        },
                        signal: controller.signal
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal memuat data');
                        return response.text();
                    })
                    .then(html => {
                        results.innerHTML = html;
                        results.style.opacity = '1';
                    })
                    .catch(error => {
                        if (error.name !== 'AbortError') {
                            results.style.opacity = '1';
                            console.error(error);
                        }
                    });
            }

            // Pencarian langsung setiap huruf diketik tanpa delay
            input.addEventListener('input', function() {
                toggleResetButton();
                loadResults(getSearchUrl());
            });

            // Filter status langsung
            status.addEventListener('change', function() {
                toggleResetButton();
                loadResults(getSearchUrl());
            });

            // Tombol Filter / Enter tetap berfungsi
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                toggleResetButton();
                loadResults(getSearchUrl());
            });

            // Pagination
            results.addEventListener('click', function(e) {
                const link = e.target.closest('.pagination a');
                if (!link) return;

                e.preventDefault();
                loadResults(link.href);
            });
        });
    </script>
@endsection
