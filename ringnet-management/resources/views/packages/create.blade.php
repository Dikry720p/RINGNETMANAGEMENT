@extends('layouts.app')

@section('title', 'Tambah Paket')

@section('content')

    <div class="max-w-2xl mx-auto space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Jenis Paket</h1>
            <p class="text-sm text-gray-500 mt-1">Tambahkan paket internet baru.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">

            <form method="POST" action="{{ route('packages.store') }}">
                @csrf

                @include('packages._form')

                <div class="flex justify-end gap-3 mt-8 border-t pt-6">

                    <a href="{{ route('packages.index') }}"
                        class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Batal
                    </a>

                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">
                        Simpan Paket
                    </button>

                </div>
            </form>

        </div>

    </div>

@endsection
