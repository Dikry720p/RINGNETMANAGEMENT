@extends('layouts.app')

@section('title', 'Catat Pembayaran')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        <div>
            <h2 class="text-2xl font-bold text-gray-800">Catat Pembayaran</h2>
            <p class="text-sm text-gray-500 mt-1">
                Pilih tagihan yang dibayarkan oleh pelanggan.
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Informasi Pelanggan --}}
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                Informasi Pelanggan
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Nama Pelanggan</p>
                    <p class="font-semibold text-gray-800">
                        {{ $customer->name }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Nama User</p>
                    <p class="font-semibold text-gray-800">
                        {{ $customer->user_name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Nomor Telepon</p>
                    <p class="font-semibold text-gray-800">
                        {{ $customer->phone ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Alamat</p>
                    <p class="font-semibold text-gray-800">
                        {{ $customer->address ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        @if ($bills->isEmpty())
            <div class="bg-white border border-gray-200 rounded-xl p-8 text-center">
                <p class="text-gray-600 font-medium">
                    Pelanggan ini tidak memiliki tagihan yang belum lunas.
                </p>

                <a href="{{ route('monthly-bills.index') }}"
                    class="inline-block mt-4 text-indigo-600 hover:text-indigo-800 font-semibold">
                    Kembali ke Pembayaran Bulanan
                </a>
            </div>
        @else
            <form action="{{ route('payments.store', $customer->id) }}" method="POST">
                @csrf

                {{-- Daftar Tagihan --}}
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                    <div class="p-6 border-b border-gray-200 flex flex-wrap justify-between items-center gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Tagihan Belum Lunas
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Pilih bulan yang ingin dibayar.
                            </p>
                        </div>

                        <button type="button" id="selectAllBills"
                            class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">
                            Pilih Semua
                        </button>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach ($bills as $bill)
                            <label class="flex items-center gap-4 p-5 hover:bg-gray-50 cursor-pointer bill-item">

                                <input type="checkbox" name="bill_ids[]" value="{{ $bill->id }}"
                                    data-amount="{{ $bill->amount }}"
                                    class="bill-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    {{ in_array($bill->id, old('bill_ids', [])) ? 'checked' : '' }}>

                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::createFromDate($bill->billing_year, $bill->billing_month, 1)->translatedFormat('F Y') }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Tagihan internet bulanan
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="font-semibold text-gray-800 whitespace-nowrap">
                                        Rp {{ number_format((float) $bill->amount, 0, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-orange-600">Belum lunas</p>
                                </div>

                            </label>
                        @endforeach
                    </div>

                    <div class="bg-indigo-50 border-t border-indigo-100 p-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 font-medium">
                                Total Pembayaran
                            </span>

                            <span id="totalPayment" class="text-2xl font-bold text-indigo-700">
                                Rp 0
                            </span>
                        </div>

                        <p id="selectedCount" class="text-sm text-gray-500 mt-2">
                            0 tagihan dipilih
                        </p>
                    </div>
                </div>

                {{-- Detail Pembayaran --}}
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-6 mt-6 space-y-5">

                    <h3 class="text-lg font-semibold text-gray-800">
                        Detail Pembayaran
                    </h3>

                    <div>
                        <label for="paid_at" class="block text-sm font-medium text-gray-700">
                            Tanggal dan Waktu Pembayaran
                        </label>

                        <input type="datetime-local" name="paid_at" id="paid_at"
                            value="{{ old('paid_at', now()->format('Y-m-d\TH:i')) }}" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">
                            Catatan (Opsional)
                        </label>

                        <textarea name="notes" id="notes" rows="3" placeholder="Contoh: Pembayaran tunai diterima oleh petugas."
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex flex-wrap gap-3 pt-2">
                        <button type="submit" id="submitPayment" disabled
                            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed">
                            Simpan Pembayaran
                        </button>

                        <a href="{{ route('monthly-bills.index') }}"
                            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold">
                            Batal
                        </a>
                    </div>

                </div>
            </form>
        @endif

    </div>

    <script>
        const checkboxes = document.querySelectorAll('.bill-checkbox');
        const totalPayment = document.getElementById('totalPayment');
        const selectedCount = document.getElementById('selectedCount');
        const submitPayment = document.getElementById('submitPayment');
        const selectAllBills = document.getElementById('selectAllBills');

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                maximumFractionDigits: 0
            }).format(number);
        }

        function updatePaymentTotal() {
            let total = 0;
            let count = 0;

            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    total += Number(checkbox.dataset.amount);
                    count++;
                }
            });

            totalPayment.textContent = 'Rp ' + formatRupiah(total);
            selectedCount.textContent = count + ' tagihan dipilih';
            submitPayment.disabled = count === 0;

            selectAllBills.textContent =
                count === checkboxes.length ? 'Batalkan Semua' : 'Pilih Semua';
        }

        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', updatePaymentTotal);
        });

        selectAllBills.addEventListener('click', function() {
            const allSelected = Array.from(checkboxes).every(function(checkbox) {
                return checkbox.checked;
            });

            checkboxes.forEach(function(checkbox) {
                checkbox.checked = !allSelected;
            });

            updatePaymentTotal();
        });

        updatePaymentTotal();
    </script>
@endsection
