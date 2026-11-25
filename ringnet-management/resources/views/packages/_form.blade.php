<div class="space-y-5">

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Nama Paket *
        </label>

        <input type="text" name="name" required value="{{ old('name', $package->name ?? '') }}"
            placeholder="Contoh: 10 Mbps" class="w-full rounded-lg border-gray-300">

        @error('name')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="price" class="block text-sm font-medium text-gray-700">
            Harga Paket (Rp)
        </label>

        <input type="number" name="price" id="price" value="{{ old('price', $package->price ?? '') }}"
            min="0" step="1000" required
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

</div>
