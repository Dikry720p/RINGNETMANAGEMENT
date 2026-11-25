<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'RINGNET MANAGEMENT')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">

    <div class="min-h-screen">

        <nav class="bg-white border-b border-gray-200">
            <div class="px-6 py-4 flex justify-between items-center">
                <h1 class="text-xl font-bold text-indigo-700">
                    RINGNET MANAGEMENT
                </h1>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">
                        {{ Auth::user()->name }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <div class="flex min-h-screen">
            <aside class="w-64 bg-slate-900 text-white shrink-0">
                <div class="p-5">
                    <p class="text-xs uppercase text-slate-400 mb-5">
                        Menu Utama
                    </p>

                    <nav class="space-y-2">
                        <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-lg hover:bg-slate-800">
                            Dashboard
                        </a>

                        <a href="{{ route('customers.index') }}" class="block px-4 py-3 rounded-lg hover:bg-slate-800">
                            Data Pelanggan
                        </a>
                        <a href="{{ route('packages.index') }}" class="block px-4 py-3 rounded-lg hover:bg-slate-800">
                            Jenis Paket
                        </a>
                        <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800">Pembayaran Bulanan</a>
                        <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800">Perlu Dipanggil</a>
                        <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800">Bermasalah</a>
                        <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800">Rekap Pendapatan</a>
                    </nav>
                </div>
            </aside>

            <main class="flex-1 p-8">
                @yield('content')
            </main>
        </div>

    </div>

</body>

</html>
