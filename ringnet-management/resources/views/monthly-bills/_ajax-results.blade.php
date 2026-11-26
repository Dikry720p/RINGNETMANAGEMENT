<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pelanggan</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No. Telepon</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Paket</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Tagihan</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal Bayar</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            @forelse ($bills as $bill)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="font-medium text-gray-800">
                            {{ $bill->customer?->name ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $bill->customer?->user_name ?? '-' }}
                        </div>
                    </td>

                    <td class="px-5 py-4 text-sm text-gray-600">
                        {{ $bill->customer?->phone ?? '-' }}
                    </td>

                    <td class="px-5 py-4 text-sm text-gray-600">
                        {{ $bill->customer?->package?->name ?? '-' }}
                    </td>

                    <td class="px-5 py-4 text-right font-semibold text-gray-800 whitespace-nowrap">
                        Rp {{ number_format((float) $bill->amount, 0, ',', '.') }}
                    </td>

                    <td class="px-5 py-4 text-center">
                        @if ($bill->status === 'PAID')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                LUNAS
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                                BELUM LUNAS
                            </span>
                        @endif
                    </td>

                    <td class="px-5 py-4 text-sm text-gray-600 whitespace-nowrap">
                        {{ $bill->paid_at ? $bill->paid_at->format('d/m/Y H:i') : '-' }}
                    </td>

                    <td class="px-5 py-4 text-center">
                        <form action="{{ route('monthly-bills.pay', $bill->id) }}" method="POST">
                            @csrf
                            @if ($bill->status === 'PAID')
                                <button type="submit"
                                    class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-semibold">
                                    LUNAS
                                </button>
                            @else
                                <button type="submit"
                                    class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-semibold">
                                    Bayar
                                </button>
                            @endif
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-500">
                        Belum ada tagihan pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="p-5">
    {{ $bills->links() }}
</div>
