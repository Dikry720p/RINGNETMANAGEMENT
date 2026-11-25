@extends('layouts.app')

@section('title', 'Edit Pelanggan')

@section('content')

    <div class="max-w-4xl mx-auto space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Pelanggan</h1>
            <p class="text-sm text-gray-500 mt-1">
                Perbarui data: {{ $customer->name }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">

            <form method="POST" action="{{ route('customers.update', $customer) }}">
                @csrf
                @method('PUT')

                @include('customers._form')

                <div class="mt-8 flex justify-end gap-3 border-t border-gray-200 pt-6">

                    <a href="{{ route('customers.index') }}"
                        class="inline-flex items-center px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Batal
                    </a>

                    <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 shadow">
                        Simpan Perubahan
                    </button>

                </div>
            </form>

        </div>

    </div>

@endsection
