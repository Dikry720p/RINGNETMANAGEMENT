<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <div>
        <label class="block text-sm font-medium mb-1">Nama Pelanggan *</label>
        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}" required
            class="w-full rounded-lg border-gray-300">
        @error('name')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Nama User</label>
        <input type="text" name="user_name" value="{{ old('user_name', $customer->user_name ?? '') }}"
            class="w-full rounded-lg border-gray-300">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Nomor Telepon</label>
        <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
            class="w-full rounded-lg border-gray-300">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Tanggal Aktivasi</label>
        <input type="date" name="activation_date"
            value="{{ old('activation_date', isset($customer) && $customer->activation_date ? $customer->activation_date->format('Y-m-d') : '') }}"
            class="w-full rounded-lg border-gray-300">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Jenis Paket</label>
        <select name="package_id" id="package_id" class="w-full rounded-lg border-gray-300">
            <option value="">-- Pilih Paket --</option>
            @foreach ($packages as $package)
                <option value="{{ $package->id }}" data-price="{{ $package->price }}" @selected(old('package_id', $customer->package_id ?? '') == $package->id)>
                    {{ $package->name }} - Rp{{ number_format($package->price, 0, ',', '.') }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Jumlah Tagihan *</label>
        <input type="number" name="billing_amount" id="billing_amount" min="0" step="0.01" required
            value="{{ old('billing_amount', $customer->billing_amount ?? 0) }}"
            class="w-full rounded-lg border-gray-300">
        @error('billing_amount')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Sales</label>
        <input type="text" name="sales" value="{{ old('sales', $customer->sales ?? '') }}"
            class="w-full rounded-lg border-gray-300">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Status Pelanggan *</label>
        <select name="customer_status" required class="w-full rounded-lg border-gray-300">
            <option value="AKTIF" @selected(old('customer_status', $customer->customer_status ?? 'AKTIF') == 'AKTIF')>
                AKTIF
            </option>
            <option value="NON-AKTIF" @selected(old('customer_status', $customer->customer_status ?? '') == 'NON-AKTIF')>
                NON-AKTIF
            </option>
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Alamat</label>
        <textarea name="address" rows="3" class="w-full rounded-lg border-gray-300">{{ old('address', $customer->address ?? '') }}</textarea>
    </div>

</div>

<script>
    document.getElementById('package_id').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const price = option.dataset.price;

        if (price !== undefined) {
            document.getElementById('billing_amount').value = price;
        }
    });
</script>
