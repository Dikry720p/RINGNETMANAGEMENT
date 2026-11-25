@extends('layouts.app')

@section('title', 'Jenis Paket')
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
                <h1 class="text-2xl font-bold text-gray-800">Jenis Paket</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola nama paket dan harga berlangganan.
                </p>
            </div>

            <a href="{{ route('packages.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                + Tambah Paket
            </a>
        </div>

        <form action="{{ route('packages.import') }}" method="POST" enctype="multipart/form-data"
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
            <div class="p-4 bg-green-100 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm p-5">

            <form method="GET" action="{{ route('packages.index') }}" class="flex flex-col sm:flex-row gap-3 mb-5">

                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama paket..."
                    class="w-full sm:max-w-md rounded-lg border-gray-300">

                <button type="submit" class="px-5 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">
                    Cari
                </button>

                <a href="{{ route('packages.index') }}"
                    class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg text-center hover:bg-gray-200">
                    Reset
                </a>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama Paket</th>
                            <th class="px-4 py-3 text-left">Harga</th>
                            <th class="px-4 py-3 text-center">Jumlah Pelanggan</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse($packages as $package)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 font-medium text-gray-800">
                                    {{ $package->name }}
                                </td>

                                <td class="px-6 py-4 font-semibold text-gray-800">
                                    Rp {{ number_format((float) $package->price, 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-4 text-center">
                                    {{ $package->customers_count }}
                                </td>

                                <td class="px-4 py-4 text-right">
                                    <a href="{{ route('packages.edit', $package) }}"
                                        class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-gray-500">
                                    Data paket belum tersedia.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $packages->links() }}
            </div>

        </div>

    </div>

@endsection
