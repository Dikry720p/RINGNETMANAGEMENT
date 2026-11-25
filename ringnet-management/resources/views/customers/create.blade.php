@extends('layouts.app')

@section('title', 'Tambah Pelanggan')

@section('content')

    <div class="max-w-4xl mx-auto space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Pelanggan</h1>
            <p class="text-sm text-gray-500 mt-1">Masukkan informasi pelanggan baru.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">

            <form method="POST" action="{{ route('customers.store') }}">
                @csrf

                @include('customers._form')

                <div class="flex justify-end gap-3 mt-6 pt-5 border-t">
                    <a href="{{ route('customers.index') }}"
                        class="px-5 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">
                        Batal
                    </a>

                    <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white">
                        Simpan Pelanggan
                    </button>
                </div>
            </form>

        </div>

    </div>

@endsection
