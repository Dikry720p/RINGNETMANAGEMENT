@extends('layouts.app')

@section('title', 'Dashboard - RINGNET MANAGEMENT')

@section('content')

    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Selamat Datang di RINGNET MANAGEMENT
            </h1>

            <p class="text-gray-500 mt-1">
                Sistem Manajemen Keuangan dan Pembayaran ISP.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Total Pelanggan</p>
                <h3 class="text-3xl font-bold mt-2">0</h3>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Potensi Tagihan</p>
                <h3 class="text-3xl font-bold mt-2">Rp0</h3>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Sudah Dibayar</p>
                <h3 class="text-3xl font-bold text-green-600 mt-2">Rp0</h3>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Belum Dibayar</p>
                <h3 class="text-3xl font-bold text-red-600 mt-2">Rp0</h3>
            </div>

        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800">
                Ringkasan Pembayaran
            </h3>

            <p class="text-sm text-gray-500 mt-2">
                Data ringkasan pembayaran akan ditampilkan setelah
                modul pembayaran selesai dibuat.
            </p>
        </div>

    </div>

@endsection
