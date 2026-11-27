<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'RINGNET MANAGEMENT')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-900">

    <div class="min-h-screen flex flex-col">

        <nav aria-label="Navigasi Utama" class="bg-white border-b border-gray-200">
            <div class="px-6 py-4 flex justify-between items-center">
                <h1 class="text-xl font-bold text-indigo-700">
                    RINGNET MANAGEMENT
                </h1>

                @auth
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">
                            {{ Auth::user()->name }}
                        </span>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">
                                Logout
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </nav>

        <div class="flex-1 flex flex-col md:flex-row">
            <aside class="w-full md:w-64 bg-slate-900 text-white shrink-0">
                <div class="p-5">
                    <p class="text-xs uppercase tracking-wider text-slate-400 mb-4">
                        Menu Utama
                    </p>

                    <nav aria-label="Menu Sidebar" class="space-y-1.5">
                        <a href="{{ route('dashboard') }}"
                            class="block px-4 py-2.5 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-200' }}">
                            Dashboard
                        </a>

                        @php
                            $isRekapKeuanganActive = request()->routeIs([
                                'customers.*',
                                'packages.*',
                                'monthly-bills.*',
                                'perlu-dipanggil.*',
                            ]);
                        @endphp

                        <details {{ $isRekapKeuanganActive ? 'open' : '' }} class="group">
                            <summary
                                class="flex items-center justify-between px-4 py-2.5 rounded-lg cursor-pointer list-none {{ $isRekapKeuanganActive ? 'bg-slate-800 text-white font-medium' : 'hover:bg-slate-800 text-slate-200' }}">
                                <span>REKAP KEUANGAN</span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 group-open:rotate-180"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </summary>

                            <div class="mt-1.5 ml-3 pl-3 border-l border-slate-700 space-y-1">
                                <a href="{{ route('customers.index') }}"
                                    class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('customers.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                    Data Pelanggan
                                </a>
                                <a href="{{ route('packages.index') }}"
                                    class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('packages.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                    Jenis Paket
                                </a>
                                <a href="{{ route('monthly-bills.index') }}"
                                    class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('monthly-bills.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                    Pembayaran Bulanan
                                </a>
                                <a href="{{ route('perlu-dipanggil.index') }}"
                                    class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('perlu-dipanggil.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                    Perlu Dipanggil
                                </a>
                                <a href="{{ route('bermasalah.index') }}"
                                    class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('bermasalah.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                    Bermasalah
                                </a>
                                <a href="#"
                                    class="block px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800 hover:text-white">
                                    Rekap Pendapatan
                                </a>
                            </div>
                        </details>
                    </nav>
                </div>
            </aside>

            <main class="flex-1 p-6 md:p-8">
                @yield('content')
            </main>
        </div>

    </div>

</body>

</html>
